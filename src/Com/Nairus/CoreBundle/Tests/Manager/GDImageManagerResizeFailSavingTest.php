<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Tests\Manager\AbstractImageManagerTest;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use phpmock\MockBuilder;

/**
 * Test of GDImageManager with <code>imagepng</code> function in failure.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManagerResizeFailSavingTest extends AbstractImageManagerTest {

    /**
     * Test <code>imagepng</code> function in failure.
     *
     * @return void
     */
    public function testFailImageSaving(): void {
        // Preparation  of the test.
        $imagePath = static::$projectDirectory . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "image-to-resize.png";
        $file = new UploadedFile($imagePath, "image-to-resize.png");
        $config = static::$imageManager->getConfig(new MockImageEntity(1));

        // Create the builtin php function mock to enable.
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName("imagepng")
                ->setFunction(function($image, $to = null, int $quality = null, int $filters = null) {
                    return false;
                })
                ->build()
                ->enable();

        // Launch the test.
        $targetPath = $config->getBaseUploadDir() . $config->getRelativeBaseDir() . "nairus-src." . $file->getExtension();
        $this->assertFalse(static::$imageManager->resize($file, $targetPath, 100, 100));
    }

}
