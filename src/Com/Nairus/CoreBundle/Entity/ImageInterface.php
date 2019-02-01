<?php

namespace Com\Nairus\CoreBundle\Entity;

/**
 * Interface for image entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ImageInterface {

    /**
     * Set imageSrcPath
     *
     * @param string $imageSrcPath
     *
     * @return ImageInterface
     */
    public function setImageSrcPath(string $imageSrcPath): ImageInterface;

    /**
     * Get imageSrcPath
     *
     * @return string
     */
    public function getImageSrcPath(): string;

    /**
     * Set imageThbPath
     *
     * @param string $imageThbPath
     *
     * @return ImageInterface
     */
    public function setImageThbPath(string $imageThbPath): ImageInterface;

    /**
     * Get imageThbPath
     *
     * @return string
     */
    public function getImageThbPath(): string;
}
