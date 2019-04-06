<?php

namespace Com\Nairus\CoreBundle\Tests\Traits;

/**
 * Trait for cleaning file and folder recursively.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait FileCleanerTrait {

    /**
     * Clean and remove folder.
     *
     * @param string $dirname The folder to remove.
     *
     * @return void
     */
    protected function cleanAndRemoveFolder(string $dirname): void {
        $dir = \opendir($dirname);
        while (false !== ( $file = \readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $dirname . DIRECTORY_SEPARATOR . $file;
                if (\is_file($full)) {
                    \unlink($full);
                } else {
                    $this->cleanAndRemoveFolder($full);
                }
            }
        }
        \closedir($dir);
        \rmdir($dirname);
    }

}
