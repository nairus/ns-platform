<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\ResumeBundle\Tests\Repository\AbstractAvatarRepositoryTestCase;
use Com\Nairus\ResumeBundle\Entity\Avatar;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Test update avatar with no image.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AvatarRepositoryUpdateWithoutImage extends AbstractAvatarRepositoryTestCase {

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
                ->setMethods(["getExtensionFromMimeType", "buildRelativePath", "getExtraFolders", "getConfig", "resize", "crop"])
                ->getMock();

        $mockImageManager
                ->expects($this->any())
                ->method("getExtensionFromMimeType")
                ->willReturn("png");

        // Define the getExtraFolders mocked method.
        $mockImageManager
                ->expects($this->any())
                ->method("getExtraFolders")
                ->willReturn("0" . $DS . "1" . $DS);

        // Define the getConfig mocked method.
        $mockImageManager
                ->expects($this->once())
                ->method("getConfig")
                ->willReturn($imageConfigDto);

        // Define the resize mocked method.
        $mockImageManager
                ->expects($this->any())
                ->method("resize")
                ->willReturn(true);

        // The crop method has to be called once.
        $mockImageManager
                ->expects($this->once())
                ->method("crop")
                ->willReturn(true);

        // Set the mock in the app container.
        static::$imageEntityListener->setImageManager($mockImageManager);
    }

    /**
     * The error case.
     *
     * @expectedException Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 4
     * @expectedExceptionMessage [ImageListener] No image to process.
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

        // force the update event without image file.
        $avatar->setOriginalName("no-image.png");
        static::$em->flush($avatar);
    }

}
