<?php

namespace Com\Nairus\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface for image entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ImageInterface {

    /**
     * Return the id of the image.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Return the relative path of the image.
     *
     * @return string
     */
    public function getRelativePath(): string;

    /**
     * Define the relative path.
     *
     * @param string $relativePath The relative path.
     *
     * @return ImageInterface
     */
    public function setRelativePath(string $relativePath): ImageInterface;

    /**
     * Return the image's extension.
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Define the image's extension.
     *
     * @return string
     */
    public function setExtension(string $extension): ImageInterface;

    /**
     * Return the original name of the image
     *
     * @return string
     */
    public function getOriginalName(): string;

    /**
     * Define the image original name.
     *
     * Used to invoked update if the extension is the same.
     *
     * @param string $originalName The original image name.
     *
     * @return ImageInterface
     */
    public function setOriginalName(string $originalName): ImageInterface;

    /**
     * Return the uploaded file (not stored in database).
     *
     * @return UploadedFile|null
     */
    public function getImageFile(): ?UploadedFile;

    /**
     * Define the uploaded file (not stored in database).
     *
     * @param UploadedFile The uploaded file
     *
     * @return ImageInterface
     */
    public function setImageFile(UploadedFile $file): ImageInterface;

    /**
     * Return the temporary extension (for update only and not stored in database).
     *
     * @return string|null
     */
    public function getTmpExtension(): ?string;

    /**
     * Define the image's extension temporary (for update update only and not stored in database).
     *
     * @param string $tmpExtension
     *
     * @return ImageInterface
     */
    public function setTmpExtension(string $tmpExtension): ImageInterface;
}
