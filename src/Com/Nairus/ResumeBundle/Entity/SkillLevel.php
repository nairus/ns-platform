<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkillLevel
 *
 * @ORM\Table(name="ns_skill_level")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\SkillLevelRepository")
 */
class SkillLevel
{
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;


    /**
     * Get id
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SkillLevel
     */
    public function setTitle(string $title) : SkillLevel
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }
}

