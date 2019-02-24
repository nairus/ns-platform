<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Experience entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 *
 * @ORM\Table(name="ns_experience")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ExperienceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Experience extends AbstractTranslatableEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $startMonth;

    /**
     * End month field
     *
     * This field is not valid if:
     *  - it's not a current job
     *  - and the end year is equal to the start year
     *  - and the end month is not greater or equal than the start month
     *
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Expression("this.getCurrentJob() or this.getEndYear() != this.getStartYear() or (this.getEndYear() == this.getStartYear() and this.getEndMonth() >= this.getStartMonth())",
     *                     message="form.errors.end-month")
     */
    private $endMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="startYear", type="smallint")
     * @Assert\NotBlank()
     */
    private $startYear;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Expression("this.getCurrentJob() or this.getEndYear() >= this.getStartYear()",
     *                     message="form.errors.end-year")
     */
    private $endYear;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Assert\Expression("this.getCurrentJob() or (this.getEndMonth() > 0 and this.getEndYear() > 0)",
     *                     message="form.errors.current-job")
     */
    private $currentJob;

    /**
     * @var Resume
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume", inversedBy="experiences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resume;

    /**
     * @Prezent\Translations(targetEntity="Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation")
     * @Assert\Valid
     */
    protected $translations;

    /**
     * Constructeur.
     */
    public function __construct() {
        parent::__construct();
        $this->currentJob = false;
        $this->translations = new ArrayCollection();
    }

    /**
     * Set company
     *
     * @param string|null $company
     *
     * @return Experience
     */
    public function setCompany(?string $company): Experience {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string|null
     */
    public function getCompany(): ?string {
        return $this->company;
    }

    /**
     * Set location
     *
     * @param string|null $location
     *
     * @return Experience
     */
    public function setLocation(?string $location): Experience {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string|null
     */
    public function getLocation(): ?string {
        return $this->location;
    }

    /**
     * Set description for current locale (proxy method).
     *
     * @param string|null $description
     *
     * @return Experience
     */
    public function setDescription(?string $description): Experience {
        /* @var $translation ExperienceTranslation */
        $translation = $this->translate();
        $translation->setDescription($description);

        return $this;
    }

    /**
     * Get description for the current locale (proxy method).
     *
     * @return string|null
     */
    public function getDescription(): ?string {
        /* @var $translation ExperienceTranslation */
        $translation = $this->translate();
        return $translation->getDescription();
    }

    /**
     * Set startMonth
     *
     * @param int $startMonth
     *
     * @return Experience
     */
    public function setStartMonth($startMonth): Experience {
        $this->startMonth = $startMonth;

        return $this;
    }

    /**
     * Get startMonth
     *
     * @return int
     */
    public function getStartMonth() {
        return $this->startMonth;
    }

    /**
     * Set endMonth
     *
     * @param int $endMonth
     *
     * @return Experience
     */
    public function setEndMonth($endMonth): Experience {
        $this->endMonth = $endMonth;

        return $this;
    }

    /**
     * Get endMonth
     *
     * @return int
     */
    public function getEndMonth() {
        return $this->endMonth;
    }

    /**
     * Set startYear
     *
     * @param int $startYear
     *
     * @return Experience
     */
    public function setStartYear($startYear): Experience {
        $this->startYear = $startYear;

        return $this;
    }

    /**
     * Get startYear
     *
     * @return int
     */
    public function getStartYear() {
        return $this->startYear;
    }

    /**
     * Set endYear
     *
     * @param int $endYear
     *
     * @return Experience
     */
    public function setEndYear($endYear): Experience {
        $this->endYear = $endYear;

        return $this;
    }

    /**
     * Get endYear
     *
     * @return int|null
     */
    public function getEndYear() {
        return $this->endYear;
    }

    /**
     * Set currentJob
     *
     * @param bool $currentJob
     *
     * @return Experience
     */
    public function setCurrentJob(bool $currentJob): Experience {
        $this->currentJob = $currentJob;

        return $this;
    }

    /**
     * Get currentJob
     *
     * @return bool
     */
    public function getCurrentJob(): bool {
        return $this->currentJob;
    }

    /**
     * Set resume
     *
     * @param Resume $resume
     *
     * @return Experience
     */
    public function setResume(Resume $resume): Experience {
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
     * Invoked before persist or update the entity.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function preSave(): void {
        // Clean endMonth and endYear fields if this is a current job.
        if ($this->currentJob) {
            $this->endMonth = null;
            $this->endYear = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationEntityClass(): string {
        return ExperienceTranslation::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function validateTranslationEntity(TranslationEntityInterface $translation): void {
        if (!$translation instanceof ExperienceTranslation) {
            throw new \TypeError("Instance of [ExperienceTranslation] expected!");
        }
    }

}
