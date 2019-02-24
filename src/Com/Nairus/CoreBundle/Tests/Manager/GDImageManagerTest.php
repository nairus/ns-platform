<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Tests\Manager\AbstractImageManagerTest;
use Com\Nairus\CoreBundle\Constants\ImageManagerConfigConstants;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Test of GDImageManager.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManagerTest extends AbstractImageManagerTest {

    /**
     * Mock of the Logger.
     *
     * @var LoggerInterface
     */
    protected $mockLogger;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        parent::setUp();
        $this->mockLogger = $this->getMockBuilder(LoggerInterface::class)
                ->setMethods(["error"])
                ->setMethodsExcept(["error" => true])
                ->getMock();
    }

    /**
     * Test the implementation and the ioc configuration of the component.
     *
     * @return void
     */
    public function testInstance(): void {
        $this->assertInstanceOf(GDImageManager::class, static::$imageManager, "1. The type of the implementation expected is not ok");
        $this->assertInstanceOf(ImageManagerInterface::class, static::$imageManager, "2. The instance has to implement [ImageManagerInterface] interface.");
    }

    /**
     * Test the <code>buildRelativePath</code> method with <code>KeyMissingException</code> thrown.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [uploads_base_dir] is missing.
     *
     * @return void
     */
    public function testConstructorWithUploadsBaseDirKeyMissing(): void {
        $badConfig = [ImageManagerConfigConstants::ENTITIES => [], ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir"];
        new GDImageManager($badConfig, $this->mockLogger);
    }

    /**
     * Test the <code>buildRelativePath</code> method with <code>KeyMissingException</code> thrown.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [relative_base_dir] is missing.
     *
     * @return void
     */
    public function testConstructorWithRelativeBaseDirKeyMissing(): void {
        $badConfig = [ImageManagerConfigConstants::ENTITIES => [], ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir"];
        new GDImageManager($badConfig, $this->mockLogger);
    }

    /**
     * Test the <code>buildRelativePath</code> method with <code>KeyMissingException</code> thrown.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [entities] is missing.
     *
     * @return void
     */
    public function testConstructorWithEntitiesBaseDirKeyMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir"
        ];
        new GDImageManager($config, $this->mockLogger);
    }

    /**
     * Test the <code>getExtraFolders</code> method.
     *
     * @return void
     */
    public function testGetExtraFolder(): void {
        $extraFolders1 = static::$imageManager->getExtraFolders(static::$mockImageEntity);
        $this->assertEquals("0" . DIRECTORY_SEPARATOR . "1" . DIRECTORY_SEPARATOR, $extraFolders1, "1. The extra folders expected are not correct.");

        // Test with 2 digit id.
        $mockImageEntity = new MockImageEntity(25);
        $extraFolders2 = static::$imageManager->getExtraFolders($mockImageEntity);
        $this->assertEquals("2" . DIRECTORY_SEPARATOR . "5" . DIRECTORY_SEPARATOR, $extraFolders2, "2. The extra folders expected are not correct.");
    }

    /**
     * Test the <code>buildRelativePath</code> method.
     *
     * @return void
     */
    public function testBuildRelativePath(): void {
        // Launch the test.
        static::$imageManager->buildRelativePath(static::$mockImageEntity);

        // Vérify the result.
        $DS = DIRECTORY_SEPARATOR;
        $this->assertEquals($DS . "tests" . $DS . "image_manager" . $DS . "mock_image_entity" . $DS, static::$mockImageEntity->getRelativePath(),
                "1. The relative path expected is not ok.");
    }

    /**
     * Test the <code>getConfig</code> method.
     *
     * @return void
     */
    public function testGetConfig(): void {
        $config = static::$imageManager->getConfig(static::$mockImageEntity);
        $this->assertRegExp("~^.+\\" . DIRECTORY_SEPARATOR . "var$~", $config->getBaseUploadDir(), "1. The base upload directory expected is not ok.");
        $this->assertEquals(DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager" . DIRECTORY_SEPARATOR,
                $config->getRelativeBaseDir(), "2. The relative base directory expected is not ok.");
        $this->assertEquals(160, $config->getSrcHeight(), "3. The source height expected is not ok");
        $this->assertEquals(160, $config->getSrcWidth(), "4. The source width expected is not ok");
        $this->assertEquals(50, $config->getThbHeight(), "3. The thumbnail height expected is not ok");
        $this->assertEquals(50, $config->getThbWidth(), "4. The thumbnail width expected is not ok");
        $this->assertTrue($config->getCrop(), "5. The crop value expected is not ok");
    }

    /**
     * Test <code>getConfig</code> with <code>src_height</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [src_height] is missing.
     *
     * @return void
     */
    public function testGetConfigWithSrcHeightMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => [
                "mock_image_entity" => [
                    ImageManagerConfigConstants::CROP => false,
                    ImageManagerConfigConstants::SRC_WIDTH => 100,
                    ImageManagerConfigConstants::THB_HEIGHT => 50,
                    ImageManagerConfigConstants::THB_WIDTH => 50,
                ]
            ]
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test <code>getConfig</code> with <code>src_width</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [src_width] is missing.
     *
     * @return void
     */
    public function testGetConfigWithSrcWidthMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => [
                "mock_image_entity" => [
                    ImageManagerConfigConstants::CROP => false,
                    ImageManagerConfigConstants::SRC_HEIGHT => 100,
                    ImageManagerConfigConstants::THB_HEIGHT => 50,
                    ImageManagerConfigConstants::THB_WIDTH => 50,
                ]
            ]
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test <code>getConfig</code> with <code>thb_height</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [thb_height] is missing.
     *
     * @return void
     */
    public function testGetConfigWithThbHeightMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => [
                "mock_image_entity" => [
                    ImageManagerConfigConstants::CROP => false,
                    ImageManagerConfigConstants::SRC_HEIGHT => 100,
                    ImageManagerConfigConstants::SRC_WIDTH => 100,
                    ImageManagerConfigConstants::THB_WIDTH => 50,
                ]
            ]
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test <code>getConfig</code> with <code>thb_width</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [thb_width] is missing.
     *
     * @return void
     */
    public function testGetConfigWithThbWidthMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => [
                "mock_image_entity" => [
                    ImageManagerConfigConstants::CROP => false,
                    ImageManagerConfigConstants::SRC_HEIGHT => 100,
                    ImageManagerConfigConstants::SRC_WIDTH => 100,
                    ImageManagerConfigConstants::THB_HEIGHT => 50,
                ]
            ]
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test <code>getConfig</code> with <code>crop</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [crop] is missing.
     *
     * @return void
     */
    public function testGetConfigWithCropMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => [
                "mock_image_entity" => [
                    ImageManagerConfigConstants::SRC_HEIGHT => 100,
                    ImageManagerConfigConstants::SRC_WIDTH => 100,
                    ImageManagerConfigConstants::THB_HEIGHT => 50,
                    ImageManagerConfigConstants::THB_WIDTH => 50,
                ]
            ]
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test <code>getConfig</code> with <code>mock_image_entity</code> key missing.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\KeyMissingException
     * @expectedExceptionMessage The key [mock_image_entity] is missing.
     *
     * @return void
     */
    public function testGetConfigWithEntityMissing(): void {
        $config = [
            ImageManagerConfigConstants::RELATIVE_BASE_DIR => "/relative/base/dir",
            ImageManagerConfigConstants::UPLOADS_BASE_DIR => "/uploads/base/dir",
            ImageManagerConfigConstants::ENTITIES => []
        ];
        $imageManager = new GDImageManager($config, $this->mockLogger);
        $imageManager->getConfig(new MockImageEntity(1));
    }

    /**
     * Test the <code>crop</code> method.
     *
     * @return void
     */
    public function testCrop(): void {
        foreach ($this->getExtensions() as $test => $ext) {
            // Preparation  of the test.
            $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.$ext";
            $file = new UploadedFile($imagePath, "image-to-crop.$ext");
            $config = static::$imageManager->getConfig(new MockImageEntity(1));

            // Launch the test.
            $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
            $result = static::$imageManager->crop($file, $targetPath, 100, 100);

            // Verify the result of the test.
            $this->assertTrue($result, "$test.1 The result expected has to be true for the extension [$ext].");
            $this->assertFileExists($targetPath, "$test.2 The target file has to be created for the extension [$ext]");
            list($width, $height) = getimagesize($targetPath);
            $this->assertEquals($width, 100, "$test.3 The width expected is not ok for the extension [$ext]");
            $this->assertEquals($height, 100, "$test.4 The height expected is not ok for the extension [$ext]");
        }
    }

    /**
     * Test the <code>crop</code> method with bad image type.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedException 3
     * @expectedExceptionMessage An error occured during creation of the image to process.
     *
     * @return void
     */
    public function testCropBadImageType(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "bad-image.bmp";
        $file = new UploadedFile($imagePath, "bad-image.bmp");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->crop($file, $targetPath, 100, 100);
    }

    /**
     * Test the <code>crop</code> method with bad width.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedException 3
     * @expectedExceptionMessage The image is smaller than the target values.
     *
     * @return void
     */
    public function testCropWithBadWidth(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->crop($file, $targetPath, 200, 100);
    }

    /**
     * Test the <code>crop</code> method with bad height.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedException 3
     * @expectedExceptionMessage The image is smaller than the target values.
     *
     * @return void
     */
    public function testCropWithBadHeight(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->crop($file, $targetPath, 100, 200);
    }

    /**
     * Test the <code>resize</code> method.
     *
     * @return void
     */
    public function testResize(): void {
        foreach ($this->getExtensions() as $test => $ext) {
            // Preparation  of the test.
            $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-resize.$ext";
            $file = new UploadedFile($imagePath, "image-to-resize.$ext");
            $config = static::$imageManager->getConfig(new MockImageEntity(1));

            // Launch the test.
            $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
            $result = static::$imageManager->resize($file, $targetPath, 100, 100);

            // Verify the result of the test.
            $this->assertTrue($result, "$test.1 The result expected has to be true for the extension [$ext].");
            $this->assertFileExists($targetPath, "$test.2 The target file has to be created for the extension [$ext]");
            list($width, $height) = getimagesize($targetPath);
            $this->assertEquals($width, 100, "$test.3 The width expected is not ok for the extension [$ext]");
            $this->assertEquals($height, 100, "$test.4 The height expected is not ok for the extension [$ext]");
        }
    }

    /**
     * Test the resize function with a different ratio.
     *
     * @return void
     */
    public function testResizeWithNotSameRatio(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        $result = static::$imageManager->resize($file, $targetPath, 100, 100);

        $this->assertTrue($result, "1. The result expected has to be true");
        $this->assertFileExists($targetPath, "2. The target file has to be created");
        list($width, $height) = getimagesize($targetPath);
        $this->assertEquals((100 * (153 / 180)), $width, "3. The width expected is not ok");
        $this->assertEquals(100, $height, "4. The height expected is not ok");
    }

    /**
     * Test the <code>crop</code> method with bad image type.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage An error occured during creation of the image to process.
     *
     * @return void
     */
    public function testResizeBadImageType(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "bad-image.bmp";
        $file = new UploadedFile($imagePath, "bad-image.bmp");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->resize($file, $targetPath, 100, 100);
    }

    /**
     * Test the <code>crop</code> method with bad width.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage The image is smaller than the target values.
     *
     * @return void
     */
    public function testResizeWithBadWidth(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->resize($file, $targetPath, 200, 100);
    }

    /**
     * Test the <code>crop</code> method with bad height.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage The image is smaller than the target values.
     *
     * @return void
     */
    public function testResizeWithBadHeight(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->resize($file, $targetPath, 100, 200);
    }

    /**
     * Return the images extensions to test.
     *
     * @return array
     */
    private function getExtensions(): array {
        return ['1' => 'png', '2' => 'gif', '3' => 'jpg'];
    }

}
