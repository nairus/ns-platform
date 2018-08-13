<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResumeSkill
 *
 * @ORM\Table(name="ns_resume_skill")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository")
 */
class ResumeSkill {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $rank;

    /**
     * @var Resume
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume", inversedBy="resumeSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resume;

    /**
     * @var Skill
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Skill")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;

    /**
     * @var SkillLevel
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\SkillLevel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skillLevel;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set rank
     *
     * @param int $rank
     *
     * @return ResumeSkill
     */
    public function setRank(int $rank): ResumeSkill {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * Set resume
     *
     * @param Resume $resume
     *
     * @return ResumeSkill
     */
    public function setResume(Resume $resume): ResumeSkill {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return Resume
     */
    public function getResume(): Resume {
        return $this->resume;
    }

    /**
     * Set skill
     *
     * @param Skill $skill
     *
     * @return ResumeSkill
     */
    public function setSkill(Skill $skill): ResumeSkill {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return Skill
     */
    public function getSkill(): Skill {
        return $this->skill;
    }

    /**
     * Set skillLevel
     *
     * @param SkillLevel $skillLevel
     *
     * @return ResumeSkill
     */
    public function setSkillLevel(SkillLevel $skillLevel): ResumeSkill {
        $this->skillLevel = $skillLevel;

        return $this;
    }

    /**
     * Get skillLevel
     *
     * @return SkillLevel
     */
    public function getSkillLevel(): SkillLevel {
        return $this->skillLevel;
    }

}
