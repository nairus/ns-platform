<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity;
use Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;
use Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * SkillLevel entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_skill_level")
 * @Gedmo\TranslationEntity(class="Com\Nairus\ResumeBundle\Entity\Translation\SkillLevelTranslation")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\SkillLevelRepository")
 */
class SkillLevel extends AbstractTranslatableEntity {

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
     * @return string| null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    use IsNewTrait;

    /**
     * {@inheritDoc}
     */
    protected function validateTranslationEntity(AbstractPersonalTranslation $translation): void {
        if (!$translation instanceof SkillLevelTranslation) {
            throw new \TypeError("Instance of [SkillLevelTranslation] expected!");
        }
    }

}
