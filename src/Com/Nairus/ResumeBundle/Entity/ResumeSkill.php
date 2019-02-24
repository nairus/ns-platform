<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ResumeSkill entity.
 *
 * @ORM\Table(name="ns_resume_skill")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository")
 * @UniqueEntity(
 *     fields={"rank", "resume"},
 *     errorPath="rank",
 *     message="form.errors.rank-not-unique"
 * )
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
     * @Assert\GreaterThan(
     *      value = 0
     * )
     */
    private $rank;

    /**
     * @var Resume
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume", inversedBy="resumeSkills")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $resume;

    /**
     * @var Skill
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Skill")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $skill;

    /**
     * @var SkillLevel
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\SkillLevel")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $skillLevel;

    use IsNewTrait;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->rank = 0;
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Set rank
     *
     * @param int|null $rank
     *
     * @return ResumeSkill
     */
    public function setRank(?int $rank): ResumeSkill {
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
    public function getSkill(): ?Skill {
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
    public function getSkillLevel(): ?SkillLevel {
        return $this->skillLevel;
    }

}
