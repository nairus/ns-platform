<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avatar
 *
 * @ORM\Table(name="ns_avatar")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\AvatarRepository")
 */
class Avatar {

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
     */
    private $imageSrcPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $imageThbPath;

    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Profile")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

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
     * @return Avatar
     */
    public function setImageSrcPath(string $imageSrcPath): Avatar {
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
     * @return Avatar
     */
    public function setImageThbPath(string $imageThbPath): Avatar {
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

    /**
     * Set profile
     *
     * @param Profile $profile
     *
     * @return Avatar
     */
    public function setProfile(Profile $profile): Avatar {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile(): Profile {
        return $this->profile;
    }

}
