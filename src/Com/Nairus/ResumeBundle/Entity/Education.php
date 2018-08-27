<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Education entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_education")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\EducationRepository")
 * @Gedmo\TranslationEntity(class="Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation")
 */
class Education extends AbstractTranslatableEntity {

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
    private $institution;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $diploma;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $domain;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $startYear;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $endYear;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Resume
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume", inversedBy="educations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resume;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set institution
     *
     * @param string $institution
     *
     * @return Education
     */
    public function setInstitution(string $institution): Education {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution(): string {
        return $this->institution;
    }

    /**
     * Set diploma
     *
     * @param string $diploma
     *
     * @return Education
     */
    public function setDiploma(string $diploma): Education {
        $this->diploma = $diploma;

        return $this;
    }

    /**
     * Get diploma
     *
     * @return string
     */
    public function getDiploma(): string {
        return $this->diploma;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return Education
     */
    public function setDomain(string $domain): Education {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain(): string {
        return $this->domain;
    }

    /**
     * Set startYear
     *
     * @param int $startYear
     *
     * @return Education
     */
    public function setStartYear(int $startYear): Education {
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
     * @param integer $endYear
     *
     * @return Education
     */
    public function setEndYear(int $endYear): Education {
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
     * Set description
     *
     * @param string $description
     *
     * @return Education
     */
    public function setDescription(string $description): Education {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Set resume
     *
     * @param Resume $resume
     *
     * @return Education
     */
    public function setResume(Resume $resume): Education {
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
    protected function validateTranslationEntity(AbstractPersonalTranslation $translation): void {
        if (!$translation instanceof EducationTranslation) {
            throw new \TypeError("Instance of [EducationTranslation] expected!");
        }
    }

}
