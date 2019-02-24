<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Tests\Manager\AbstractImageManagerTest;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use phpmock\MockBuilder;

/**
 * Test of GDImageManager with <code>imagecopyresampled</code> function in failure.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManagerResizeFailImageResampledTest extends AbstractImageManagerTest {

    /**
     * Test <code>imagecopyresampled</code> function in failure.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage An error occured while processing the image with [imagecopyresampled] function.
     *
     * @return void
     */
    public function testFailImageCopyResampled(): void {
        // preparation of the test
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-resize.png";
        $file = new UploadedFile($imagePath, "image-to-crop.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Create the builtin php function mock to enable.
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName("imagecopyresampled")
                ->setFunction(function($dst_image, $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_w, int $dst_h, int $src_w, int $src_h): bool {
                    return false;
                })
                ->build()
                ->enable();

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->resize($file, $targetPath, 100, 100);
    }

}
