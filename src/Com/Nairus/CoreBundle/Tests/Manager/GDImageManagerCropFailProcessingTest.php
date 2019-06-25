<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Tests\Manager\AbstractImageManagerTest;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use phpmock\MockBuilder;

/**
 * Test of GDImageManager with isolated process for mocking `imagecrop` builtin function.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManagerCropFailProcessingTest extends AbstractImageManagerTest {

    /**
     * Test the `crop` method with bad image type.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage An error occured while processing the image.
     *
     * @return void
     */
    public function testCropFailProcessing(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-crop.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Create the builtin php function mock to enable.
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName("imagecrop")
                ->setFunction(function ($image, array $rect) {
                    return false;
                })
                ->build()
                ->enable();

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->crop($file, $targetPath, 100, 100);
    }

}
