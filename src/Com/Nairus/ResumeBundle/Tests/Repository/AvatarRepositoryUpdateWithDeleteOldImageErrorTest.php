<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\ResumeBundle\Entity\Avatar;
use Com\Nairus\ResumeBundle\Tests\Repository\AbstractAvatarRepositoryTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use phpmock\MockBuilder;

/**
 * Test update Avatar with error while deleting old images.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AvatarRepositoryUpdateWithDeleteOldImageErrorTest extends AbstractAvatarRepositoryTestCase {

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

        // Define the getConfig mocked method called twice (once after persist and another once after update).
        $mockImageManager
                ->expects($this->exactly(2))
                ->method("getConfig")
                ->willReturn($imageConfigDto);

        // Define the resize mocked method called exactly 4 times (twice after persist and another twice after update).
        $mockImageManager
                ->expects($this->exactly(4))
                ->method("resize")
                ->willReturn(true);

        // The crop method has to be called twice (once after persist and another once after update).
        $mockImageManager
                ->expects($this->exactly(2))
                ->method("crop")
                ->willReturn(true);

        // Set the mock in the app container.
        static::$imageEntityListener->setImageManager($mockImageManager);

        // Create the builtin php function mock to enable.
        $reflectionClass = new \ReflectionClass(\Com\Nairus\CoreBundle\Listener\ImageEntityListener::class);
        $builder = new MockBuilder();
        $builder->setNamespace($reflectionClass->getNamespaceName())
                ->setName("unlink")
                ->setFunction(function (string $filename, $context = null): bool {
                    throw new \Error("Unable to delete the file: $filename");
                })
                ->build()
                ->enable();
    }

    /**
     * Test throwing an error while deleting old image.
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
                ->setTmpExtension("png")
                ->setImageFile(new UploadedFile($newPath, "image-to-crop.jpg"));
        // no exception is expected after updating the entity.
        static::$em->flush($avatar);
    }

}
