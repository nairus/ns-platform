<?php

namespace Com\Nairus\CoreBundle\Tests\Manager;

use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\Entity\Mock\MockImageEntity;

/**
 * Abstract class for image manager test classes.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractImageManagerTest extends AbstractKernelTestCase {

    /**
     * Mock of the ImageInterface.
     *
     * @var MockImageEntity
     */
    protected static $mockImageEntity;

    /**
     * Instance of ImageManager.
     *
     * @var ImageManagerInterface
     */
    protected static $imageManager;

    /**
     * Project directory.
     *
     * @var string
     */
    protected static $projectDirectory;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        static::$projectDirectory = static::$container->getParameter('kernel.project_dir');
        static::$mockImageEntity = new MockImageEntity(1);
        static::$imageManager = static::$kernel->getContainer()->get("ns_core.image_manager");
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        // Create the target image directory
        $dirname = static::$projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager";
        if (!\is_dir($dirname)) {
            \mkdir($dirname);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // Remove the target image directory
        $dirname = static::$projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager";
        if (\is_dir($dirname)) {
            $dir = \opendir($dirname);
            while (false !== ( $file = \readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    $full = $dirname . DIRECTORY_SEPARATOR . $file;
                    \unlink($full);
                }
            }
            \closedir($dir);
            \rmdir($dirname);
        }

        // Disable all mocks.
        \phpmock\Mock::disableAll();
    }

}
