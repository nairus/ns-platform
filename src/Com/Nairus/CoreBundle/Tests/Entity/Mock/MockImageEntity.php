<?php

namespace Com\Nairus\CoreBundle\Tests\Entity\Mock;

use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Mock of ImageInterface entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class MockImageEntity implements ImageInterface {

    /**
     * The id of the entity.
     *
     * @var int
     */
    private $id;

    /**
     * The relative path.
     *
     * @var string
     */
    private $relativePath;

    /**
     * The image's extension.
     *
     * @var string
     */
    private $extension;

    /**
     * The uploaded image file.
     *
     * @var UploadedFile
     */
    private $imageFile;

    /**
     * The temporary extension.
     *
     * @var string
     */
    private $tmpExtension;

    /**
     * Constructor.
     *
     * @param int $id The id of the entity.
     */
    public function __construct(int $id) {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getImageFile(): ?UploadedFile {
        return $this->imageFile;
    }

    /**
     * {@inheritDoc}
     */
    public function setImageFile(UploadedFile $file): ImageInterface {
        $this->imageFile = $file;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtension(): string {
        return $this->extension;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtension(string $extension): ImageInterface {
        $this->extension = $extension;
    }

    /**
     * {@inheritDoc}
     */
    public function setTmpExtension(string $tmpExtension): ImageInterface {
        $this->tmpExtension = $tmpExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function getTmpExtension(): string {
        return $this->tmpExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelativePath(): string {
        return $this->relativePath;
    }

    /**
     * {@inheritDoc}
     */
    public function setRelativePath(string $relativePath): ImageInterface {
        $this->relativePath = $relativePath;
        return $this;
    }

}
