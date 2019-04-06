<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Com\Nairus\CoreBundle\Exception\KeyMissingException;
use Com\Nairus\CoreBundle\Exception\ImageProcessingException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Image manager interface.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ImageManagerInterface {

    /**
     * Return the extension from image mimetype.
     *
     * @param string $mimeType The image mimetype.
     *
     * @return string
     */
    public function getExtensionFromMimeType(string $mimeType): string;

    /**
     * Get the extra folders for the path (2 last digits).
     *
     * @param ImageInterface $imageEntity The image entity.
     *
     * @return string
     */
    public function getExtraFolders(ImageInterface $imageEntity): string;

    /**
     * Build the relative path of the image to persist in database.
     *
     * @param ImageInterface $imageEntity The image entity.
     *
     * @return void
     *
     * @throws KeyMissingException In case of bad configuration
     */
    public function buildRelativePath(ImageInterface $imageEntity): void;

    /**
     * Crop the image.
     *
     * @param File   $source The source image to crop.
     * @param string $target The target image.
     * @param int    $width  The width of the aera to crop.
     * @param int    $height The height of the aera to crop.
     *
     * @return bool
     *
     * @throws ImageProcessingException If an error occurs during image processing.
     */
    public function crop(File $source, string $target, int $width, int $height): bool;

    /**
     * Return the config of the entity.
     *
     * @param ImageInterface $imageEntity The image entity.
     *
     * @return ImageConfigDto
     *
     * @throws KeyMissingException In case of bad configuration
     */
    public function getConfig(ImageInterface $imageEntity): ImageConfigDto;

    /**
     * Resize the image.
     *
     * @param File   $source The source image to crop.
     * @param string $target The target image.
     * @param int    $width  The width of the aera to resize.
     * @param int    $height The height of the aera to resize.
     *
     * @return bool
     *
     * @throws ImageProcessingException If an error occurs during image processing.
     */
    public function resize(File $source, string $target, int $width, int $height): bool;
}
