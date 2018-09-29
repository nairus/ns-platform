<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation;
use Com\Nairus\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resume entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_resume")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ResumeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Resume extends AbstractTranslatableEntity {

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $anonymous;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Com\Nairus\ResumeBundle\Entity\ResumeSkill", mappedBy="resume")
     */
    private $resumeSkills;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Com\Nairus\ResumeBundle\Entity\Experience", mappedBy="resume")
     */
    private $experiences;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Com\Nairus\ResumeBundle\Entity\Education", mappedBy="resume")
     */
    private $educations;

    /**
     * @Prezent\Translations(targetEntity="Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation")
     * @Assert\Valid
     */
    protected $translations;

    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    public function __construct() {
        parent::__construct();
        $this->status = ResumeStatusEnum::OFFLINE_INCOMPLETE;
        $this->anonymous = false;
        $this->resumeSkills = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->educations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set anonymous
     *
     * @param bool $anonymous
     *
     * @return Resume
     */
    public function setAnonymous(bool $anonymous): Resume {
        $this->anonymous = $anonymous;

        return $this;
    }

    /**
     * Get anonymous
     *
     * @return bool
     */
    public function getAnonymous(): bool {
        return $this->anonymous;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Resume
     */
    public function setStatus(int $status): Resume {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * Set author
     *
     * @param User $author
     *
     * @return Resume
     */
    public function setAuthor(User $author): Resume {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor(): User {
        return $this->author;
    }

    /**
     * Add resumeSkill
     *
     * @param ResumeSkill $resumeSkill
     *
     * @return Resume
     */
    public function addResumeSkill(ResumeSkill $resumeSkill): Resume {
        $this->resumeSkills[] = $resumeSkill;

        return $this;
    }

    /**
     * Remove resumeSkill
     *
     * @param ResumeSkill $resumeSkill
     */
    public function removeResumeSkill(ResumeSkill $resumeSkill) {
        $this->resumeSkills->removeElement($resumeSkill);
    }

    /**
     * Get resumeSkills
     *
     * @return Collection <ResumeSkill>
     */
    public function getResumeSkills(): Collection {
        return $this->resumeSkills;
    }

    /**
     * Add experience
     *
     * @param Experience $experience
     *
     * @return Resume
     */
    public function addExperience(Experience $experience): Resume {
        $this->experiences[] = $experience;

        return $this;
    }

    /**
     * Remove experience
     *
     * @param Experience $experience
     */
    public function removeExperience(Experience $experience) {
        $this->experiences->removeElement($experience);
    }

    /**
     * Get experiences
     *
     * @return Collection <Experience>
     */
    public function getExperiences(): Collection {
        return $this->experiences;
    }

    /**
     * Add education
     *
     * @param Education $education
     *
     * @return Resume
     */
    public function addEducation(Education $education): Resume {
        $this->educations[] = $education;

        return $this;
    }

    /**
     * Remove education
     *
     * @param Education $education
     */
    public function removeEducation(Education $education) {
        $this->educations->removeElement($education);
    }

    /**
     * Get educations
     *
     * @return Collection <Education>
     */
    public function getEducations(): Collection {
        return $this->educations;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Resume
     */
    public function setIp(string $ip): Resume {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp(): string {
        return $this->ip;
    }

    /**
     * Set title for current locale (proxy method).
     *
     * @param string $title
     *
     * @return ResumeTranslation
     */
    public function setTitle(string $title): ResumeTranslation {
        /* @var $translation ResumeTranslation */
        $translation = $this->translate();

        return $translation->setTitle($title);
    }

    /**
     * Get title for current locale (proxy method).
     *
     * @return string|null
     */
    public function getTitle(): ?string {
        /* @var $translation ResumeTranslation */
        $translation = $this->translate();

        return $translation->getTitle();
    }

    /**
     * Set slug for current locale (proxy method).
     *
     * @param string $slug
     *
     * @return Resume
     */
    public function setSlug(string $slug): ResumeTranslation {
        /* @var $translation ResumeTranslation */
        $translation = $this->translate();

        return $translation->setSlug($slug);
    }

    /**
     * Get slug for current method (proxy method).
     *
     * @return string
     */
    public function getSlug(): string {
        /* @var $translation ResumeTranslation */
        $translation = $this->translate();

        return $translation->getSlug();
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationEntityClass(): string {
        return ResumeTranslation::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function validateTranslationEntity(TranslationEntityInterface $translation): void {
        if (!$translation instanceof ResumeTranslation) {
            throw new \TypeError("Instance of [ResumeTranslation] expected!");
        }
    }

}
