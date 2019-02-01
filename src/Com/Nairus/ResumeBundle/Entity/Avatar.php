<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Avatar
 *
 * @ORM\Table(name="ns_avatar")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\AvatarRepository")
 */
class Avatar implements ImageInterface {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $imageSrcPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $imageThbPath;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set imageSrcPath
     *
     * @param string $imageSrcPath
     *
     * @return ImageInterface
     */
    public function setImageSrcPath(string $imageSrcPath): ImageInterface {
        $this->imageSrcPath = $imageSrcPath;

        return $this;
    }

    /**
     * Get imageSrcPath
     *
     * @return string
     */
    public function getImageSrcPath(): string {
        return $this->imageSrcPath;
    }

    /**
     * Set imageThbPath
     *
     * @param string $imageThbPath
     *
     * @return ImageInterface
     */
    public function setImageThbPath(string $imageThbPath): ImageInterface {
        $this->imageThbPath = $imageThbPath;

        return $this;
    }

    /**
     * Get imageThbPath
     *
     * @return string
     */
    public function getImageThbPath(): string {
        return $this->imageThbPath;
    }

}
