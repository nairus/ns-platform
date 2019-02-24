<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Tests\Manager\AbstractImageManagerTest;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use phpmock\MockBuilder;

/**
 * Test of GDImageManager with <code>imagecreatetruecolor</code> function in failure.
 *
 * This test has to be launch before <code>GDImageManagerResizeFailImageResampledTest</code> because of the mocked builtin function.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManagerResizeFailImageCreationTest extends AbstractImageManagerTest {

    /**
     * Test <code>imagecreatetruecolor</code> function in failure.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\ImageProcessingException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage An error occured while processing the image with [imagecreatetruecolor] function.
     *
     * @return void
     */
    public function testFailImageCreation(): void {
        // preparation of the test
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-resize.png";
        $file = new UploadedFile($imagePath, "image-to-resize.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Create the builtin php function mock to enable.
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName("imagecreatetruecolor")
                ->setFunction(function(int $width, int $height) {
                    return false;
                })
                ->build()
                ->enable();

        //Launch the test .
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        static::$imageManager->resize($file, $targetPath, 100, 100);
    }

}
