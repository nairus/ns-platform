<?php

namespace Com\Nairus\CoreBundle\Dto;

/**
 * DTO for an image entity configuration.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ImageConfigDto {

    /**
     * The base upload directory.
     *
     * @var string
     */
    private $baseUploadDir;

    /**
     * The relative base dir.
     *
     * @var string
     */
    private $relativeBaseDir;

    /**
     * The source image width.
     *
     * @var int
     */
    private $srcWidth;

    /**
     * The source image height.
     *
     * @var int
     */
    private $srcHeight;

    /**
     * The thmbnail image width.
     *
     * @var int
     */
    private $thbWidth;

    /**
     * The thmbnail image height.
     *
     * @var int
     */
    private $thbHeight;

    /**
     * Define if the image has to be cropped.
     *
     * @var bool
     */
    private $crop = false;

    /**
     * Get the base upload directory.
     *
     * @return string
     */
    public function getBaseUploadDir(): string {
        return $this->baseUploadDir;
    }

    /**
     * Define the base upload directory.
     *
     * @param string $baseUploadDir The base upload directory.
     *
     * @return ImageConfigDto
     */
    public function setBaseUploadDir(string $baseUploadDir): ImageConfigDto {
        $this->baseUploadDir = $baseUploadDir;
        return $this;
    }

    /**
     * Return the relative base directory.
     *
     * @return string
     */
    public function getRelativeBaseDir(): string {
        return $this->relativeBaseDir;
    }

    /**
     * Define the relative base directory.
     *
     * @param string $relativeBaseDir The relative base directory.
     *
     * @return ImageConfigDto
     */
    public function setRelativeBaseDir(string $relativeBaseDir): ImageConfigDto {
        $this->relativeBaseDir = $relativeBaseDir;
        return $this;
    }

    /**
     * Return the source image width.
     *
     * @return int
     */
    public function getSrcWidth(): int {
        return $this->srcWidth;
    }

    /**
     * Define the source image width.
     *
     * @param int $srcWidth The source image width.
     *
     * @return ImageConfigDto
     */
    public function setSrcWidth(int $srcWidth): ImageConfigDto {
        $this->srcWidth = $srcWidth;
        return $this;
    }

    /**
     * Return the source image height.
     *
     * @return int
     */
    public function getSrcHeight(): int {
        return $this->srcHeight;
    }

    /**
     * Défine the source image height.
     *
     * @param int $srcHeight The source image height.
     *
     * @return ImageConfigDto
     */
    public function setSrcHeight(int $srcHeight): ImageConfigDto {
        $this->srcHeight = $srcHeight;
        return $this;
    }

    /**
     *  Return the thumbnail witdh.
     *
     * @return int
     */
    public function getThbWidth(): int {
        return $this->thbWidth;
    }

    /**
     * Define the thumbnail witdh.
     *
     * @param int $thbWidth The thumbnail witdh.
     *
     * @return ImageConfigDto
     */
    public function setThbWidth(int $thbWidth): ImageConfigDto {
        $this->thbWidth = $thbWidth;
        return $this;
    }

    /**
     * Return the thumbnail height.
     *
     * @return int
     */
    public function getThbHeight(): int {
        return $this->thbHeight;
    }

    /**
     * Define the thumbnail height.
     *
     * @param int $thbHeight The thumbnail height.
     *
     * @return ImageConfigDto
     */
    public function setThbHeight(int $thbHeight): ImageConfigDto {
        $this->thbHeight = $thbHeight;
        return $this;
    }

    /**
     * Return `true` if the image is cropped, `false` otherwise.
     *
     * @return bool
     */
    public function getCrop(): bool {
        return $this->crop;
    }

    /**
     * Define if the image is cropped or not.
     *
     * @param bool $crop The image crop status
     *
     * @return ImageConfigDto
     */
    public function setCrop(bool $crop): ImageConfigDto {
        $this->crop = $crop;
        return $this;
    }

}
