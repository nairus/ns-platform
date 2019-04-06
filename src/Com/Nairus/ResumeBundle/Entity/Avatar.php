<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Avatar entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_avatar")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\AvatarRepository")
 */
class Avatar implements ImageInterface {

    /**
     * The id of the image.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The relative path.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $relativePath;

    /**
     * The image's extension (jpg, jpeg, gif, png).
     *
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     */
    private $extension;

    /**
     * The image original name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $originalName;

    /**
     * The uploaded image file.
     *
     * @var UploadedFile
     *
     * @Assert\Image(
     *   minWidth = 150,
     *   maxRatio = 1,
     *   minRatio = 1,
     *   mimeTypes = {"image/gif", "image/jpeg", "image/png"}
     * )
     */
    private $imageFile;

    /**
     * The temporary extension.
     *
     * @var string
     */
    private $tmpExtension;

    use \Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
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
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalName(): string {
        return $this->originalName;
    }

    /**
     * {@inheritDoc}
     */
    public function setOriginalName(string $originalName): ImageInterface {
        $this->originalName = $originalName;
        return $this;
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
    public function getTmpExtension(): ?string {
        return $this->tmpExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function setTmpExtension(string $tmpExtension): ImageInterface {
        $this->tmpExtension = $tmpExtension;
        return $this;
    }

}
