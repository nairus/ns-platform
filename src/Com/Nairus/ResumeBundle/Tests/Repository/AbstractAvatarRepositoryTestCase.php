<?php

namespace Com\Nairus\ResumeBundle\Tests\Repository;

use Com\Nairus\CoreBundle\Listener\ImageEntityListener;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;

/**
 * Abstract class AvatarRepository test classes.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractAvatarRepositoryTestCase extends AbstractKernelTestCase {

    /**
     * ImageEntityListener instance from container.
     *
     * @var ImageEntityListener
     */
    protected static $imageEntityListener;

    /**
     * Project directory.
     *
     * @var string
     */
    protected static $projectDirectory;

    /**
     * @var AvatarRepository
     */
    protected static $repository;

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\FileCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$projectDirectory = static::$container->getParameter('kernel.project_dir');
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Avatar");
        static::$imageEntityListener = static::$container->get("ns_core.image_entity_listener");
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
        // Remove the target image directory
        $dirname = static::$projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager";
        if (\is_dir($dirname)) {
            $this->cleanAndRemoveFolder($dirname);
        }

        // Truncate the avatar table.
        $this->cleanDatas([\Com\Nairus\ResumeBundle\Entity\Avatar::class]);

        // Disable all mocks.
        \phpmock\Mock::disableAll();

        // Set the original manager in the listener.
        static::$imageEntityListener->setImageManager(static::$container->get("ns_core.image_manager"));

        parent::tearDown();
    }

    /**
     * Find an image in the upload folder.
     *
     * @param string $imageName The image name to find.
     *
     * @return bool
     */
    protected function findImage(string $imageName): bool {
        $DS = DIRECTORY_SEPARATOR;
        $dirname = static::$projectDirectory . $DS . "var" . $DS . "tests" . $DS . "image_manager" . $DS . "avatar";

        if (\is_dir($dirname)) {
            return $this->recursiveFindImage($dirname, $imageName);
        }

        return false;
    }

    /**
     * Recursive finding image name.
     *
     * @param string $dirname   The current directory.
     * @param string $imageName The image name to find.
     *
     * @return bool <code>true</code> if the image is found, <code>false</code> otherwise.
     */
    private function recursiveFindImage(string $dirname, string $imageName): bool {
        $dir = \opendir($dirname);
        while (false !== ( $file = \readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (\is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
                    $found = $this->recursiveFindImage($dirname . DIRECTORY_SEPARATOR . $file, $imageName);
                    if ($found) {
                        \closedir($dir);
                        return $found;
                    }
                } elseif ($file === $imageName) {
                    \closedir($dir);
                    return true;
                }
            }
        }
        \closedir($dir);
        return false;
    }

}
