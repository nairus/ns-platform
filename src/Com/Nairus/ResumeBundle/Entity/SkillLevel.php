<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SkillLevel
 *
 * @ORM\Table(name="ns_skill_level")
 * @Gedmo\TranslationEntity(class="Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\SkillLevelRepository")
 */
class SkillLevel {

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
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SkillLevel
     */
    public function setTitle(string $title): SkillLevel {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->translations = new ArrayCollection();
    }

    /**
     * Add translation.
     *
     * @param \Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation $translation
     *
     * @return SkillLevel
     */
    public function addTranslation(SkillLevelTranslation $translation): SkillLevel {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation.
     *
     * @param SkillLevelTranslation $translation
     *
     * @return boolean <code>TRUE</code> if this collection contained the specified element, <code>FALSE</code> otherwise.
     */
    public function removeTranslation(SkillLevelTranslation $translation): bool {
        return $this->translations->removeElement($translation);
    }

    /**
     * Get translations.
     *
     * @return Collection
     */
    public function getTranslations(): Collection {
        return $this->translations;
    }

}
