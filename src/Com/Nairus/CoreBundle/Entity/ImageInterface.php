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
     * Return the uploaed file.
     *
     * @return UploadedFile|null
     */
    public function getImageFile(): ?UploadedFile;

    /**
     * Define the uploaded file.
     *
     * @param UploadedFile The uploaded file
     *
     * @return UploadedFile
     */
    public function setImageFile(UploadedFile $file): ImageInterface;

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
