<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkillLevelTranslation Entity
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_skill_level_translations")
 */
class SkillLevelTranslation extends AbstractTranslationEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      max = 100
     * )
     */
    private $title;

    /**
     * @Prezent\Translatable(targetEntity="Com\Nairus\ResumeBundle\Entity\SkillLevel")
     */
    protected $translatable;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SkillLevel
     */
    public function setTitle(?string $title): SkillLevelTranslation {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntityInterface $object): void {
        if (!$object instanceof SkillLevel) {
            throw new \TypeError("Instance of [SkillLevel] expected!");
        }
    }

}
