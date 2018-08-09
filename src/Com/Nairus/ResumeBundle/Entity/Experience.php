<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Experience
 *
 * @ORM\Table(name="ns_experience")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ExperienceRepository")
 */
class Experience
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
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="startMonth", type="smallint")
     */
    private $startMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="endMonth", type="smallint", nullable=true)
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
     * @ORM\Column(name="endYear", type="smallint", nullable=true)
     */
    private $endYear;

    /**
     * @var bool
     *
     * @ORM\Column(name="currentJob", type="boolean")
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
     * Constructeur.
     */
    public function __construct()
    {
        $this->currentJob = false;
    }

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
     * Set company
     *
     * @param string $company
     *
     * @return Experience
     */
    public function setCompany(string $company) : Experience
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany() : string
    {
        return $this->company;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Experience
     */
    public function setLocation(string $location) : Experience
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation() : string
    {
        return $this->location;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Experience
     */
    public function setDescription(string $description) : Experience
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set startMonth
     *
     * @param int $startMonth
     *
     * @return Experience
     */
    public function setStartMonth(int $startMonth) : Experience
    {
        $this->startMonth = $startMonth;

        return $this;
    }

    /**
     * Get startMonth
     *
     * @return int
     */
    public function getStartMonth() : int
    {
        return $this->startMonth;
    }

    /**
     * Set endMonth
     *
     * @param int $endMonth
     *
     * @return Experience
     */
    public function setEndMonth(int $endMonth) : Experience
    {
        $this->endMonth = $endMonth;

        return $this;
    }

    /**
     * Get endMonth
     *
     * @return int
     */
    public function getEndMonth() : int
    {
        return $this->endMonth;
    }

    /**
     * Set startYear
     *
     * @param int $startYear
     *
     * @return Experience
     */
    public function setStartYear(int $startYear) : Experience
    {
        $this->startYear = $startYear;

        return $this;
    }

    /**
     * Get startYear
     *
     * @return int
     */
    public function getStartYear() : int
    {
        return $this->startYear;
    }

    /**
     * Set endYear
     *
     * @param int $endYear
     *
     * @return Experience
     */
    public function setEndYear(int $endYear) : Experience
    {
        $this->endYear = $endYear;

        return $this;
    }

    /**
     * Get endYear
     *
     * @return int
     */
    public function getEndYear() : int
    {
        return $this->endYear;
    }

    /**
     * Set currentJob
     *
     * @param bool $currentJob
     *
     * @return Experience
     */
    public function setCurrentJob(bool $currentJob) : Experience
    {
        $this->currentJob = $currentJob;

        return $this;
    }

    /**
     * Get currentJob
     *
     * @return bool
     */
    public function getCurrentJob() : bool
    {
        return $this->currentJob;
    }

    /**
     * Set resume
     *
     * @param Resume $resume
     *
     * @return Experience
     */
    public function setResume(Resume $resume) : Experience
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return Resume
     */
    public function getResume() : Resume
    {
        return $this->resume;
    }
}
