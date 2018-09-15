<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * Experience
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_experience")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ExperienceRepository")
 */
class Experience extends AbstractTranslatableEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $startMonth;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $endMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="startYear", type="smallint")
     */
    private $startYear;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $endYear;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
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
     * @param string $company
     *
     * @return Experience
     */
    public function setCompany(string $company): Experience {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany(): string {
        return $this->company;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Experience
     */
    public function setLocation(string $location): Experience {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * Set description for current locale (proxy method).
     *
     * @param string $description
     *
     * @return ExperienceTranslation
     */
    public function setDescription(string $description): ExperienceTranslation {
        /* @var $translation ExperienceTranslation */
        $translation = $this->translate();

        return $translation->setDescription($description);
    }

    /**
     * Get description for the current locale (proxy method).
     *
     * @return string
     */
    public function getDescription(): string {
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
    public function setStartMonth(int $startMonth): Experience {
        $this->startMonth = $startMonth;

        return $this;
    }

    /**
     * Get startMonth
     *
     * @return int
     */
    public function getStartMonth(): int {
        return $this->startMonth;
    }

    /**
     * Set endMonth
     *
     * @param int $endMonth
     *
     * @return Experience
     */
    public function setEndMonth(int $endMonth): Experience {
        $this->endMonth = $endMonth;

        return $this;
    }

    /**
     * Get endMonth
     *
     * @return int
     */
    public function getEndMonth(): int {
        return $this->endMonth;
    }

    /**
     * Set startYear
     *
     * @param int $startYear
     *
     * @return Experience
     */
    public function setStartYear(int $startYear): Experience {
        $this->startYear = $startYear;

        return $this;
    }

    /**
     * Get startYear
     *
     * @return int
     */
    public function getStartYear(): int {
        return $this->startYear;
    }

    /**
     * Set endYear
     *
     * @param int $endYear
     *
     * @return Experience
     */
    public function setEndYear(int $endYear): Experience {
        $this->endYear = $endYear;

        return $this;
    }

    /**
     * Get endYear
     *
     * @return int
     */
    public function getEndYear(): int {
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
