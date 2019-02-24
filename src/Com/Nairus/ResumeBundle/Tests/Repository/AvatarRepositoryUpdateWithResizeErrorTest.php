<?php

use Com\Nairus\ResumeBundle\Tests\Repository\AbstractAvatarRepositoryTestCase;
use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Exception\ImageProcessingException;
use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\ResumeBundle\Entity\Avatar;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Test update Avatar with error while resizing the new image.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AvatarRepositoryUpdateWithResizeErrorTest extends AbstractAvatarRepositoryTestCase {

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        parent::setUp();

        // Set the mocks.
        $DS = DIRECTORY_SEPARATOR;
        $uploadBaseDir = static::$projectDirectory . $DS . "var";
        $this->relativeBaseDir = $DS . "tests" . $DS . "image_manager" . $DS;
        $imageConfigDto = new ImageConfigDto();
        $imageConfigDto->setBaseUploadDir($uploadBaseDir)
                ->setCrop(true)
                ->setRelativeBaseDir($this->relativeBaseDir)
                ->setSrcHeight(100)
                ->setSrcWidth(100)
                ->setThbHeight(50)
                ->setThbWidth(50);

        $mockImageManager = $this->getMockBuilder(ImageManagerInterface::class)
                ->disableOriginalConstructor()
                ->setMethods(["buildRelativePath", "getExtraFolders", "getConfig", "resize", "crop"])
                ->getMock();

        // Define the getExtraFolders mocked method.
        $mockImageManager
                ->expects($this->any())
                ->method("getExtraFolders")
                ->willReturn("0" . $DS . "1" . $DS);

        // Define the getConfig mocked method.
        $mockImageManager
                ->expects($this->exactly(2))
                ->method("getConfig")
                ->willReturn($imageConfigDto);

        // Define the resize mocked method called 3 times (twice after persist and once after update).
        $resizeException = new ImageProcessingException("An error occured", ImageProcessingException::RESIZE_ERROR);

        $mockImageManager
                ->expects($this->exactly(3))
                ->method("resize")
                ->willReturnOnConsecutiveCalls(true, true, $this->throwException($resizeException));

        // The crop method has to be never called.
        $mockImageManager
                ->expects($this->once())
                ->method("crop")
                ->willReturn(true);

        // Set the mock in the app container.
        static::$imageEntityListener->setImageManager($mockImageManager);
    }

    /**
     * Test throwing an error while deleting old image.
     *
     * @expectedException Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 2
     *
     * @return void
     */
    public function testErrorCase(): void {
        // Define the relative base dir to simulate the `buildRelativePath` method mocked.
        $DS = DIRECTORY_SEPARATOR;
        $path = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "image-to-crop.png";
        $newAvatar = new Avatar();
        $newAvatar->setImageFile(new UploadedFile($path, "image-to-crop.png"))
                ->setRelativePath($this->relativeBaseDir . "avatar" . $DS);

        static::$em->persist($newAvatar);
        static::$em->flush();
        static::$em->clear();

        $avatars = static::$repository->findAll();
        $this->assertCount(1, $avatars, "1. One entity is expected in database.");
        /* @var $avatar Avatar */
        $avatar = $avatars[0];

        // try to update with a new image.
        $newPath = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "image-to-crop.jpg";
        $avatar->setExtension("jpg")
                ->setImageFile(new UploadedFile($newPath, "image-to-crop.jpg"));
        static::$em->flush($avatar);
    }

}
