<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkillLevel entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_skill_level")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\SkillLevelRepository")
 */
class SkillLevel extends AbstractTranslatableEntity {

    /**
     * @Prezent\Translations(targetEntity="Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation")
     * @Assert\Valid
     */
    protected $translations;

    /**
     * Return the title for current language (proxy getter).
     *
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->translate()->getTitle();
    }

    /**
     * Set title for current language (proxy setter).
     *
     * @param string $title The title for the current language.
     *
     * @return SkillLevelTranslation
     */
    public function setTitle(string $title) {
        return $this->translate()->setTitle($title);
    }

    /**
     * Return the entity class for transalations.
     *
     * @return string
     */
    public static function getTranslationEntityClass(): string {
        return SkillLevelTranslation::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function validateTranslationEntity(TranslationEntityInterface $translation): void {
        if (!$translation instanceof SkillLevelTranslation) {
            throw new \TypeError("Instance of [SkillLevelTranslation] expected!");
        }
    }

}
