<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ExperienceTransalation Entity
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_experience_translations")
 */
class ExperienceTranslation extends AbstractTranslationEntity {

    /**
     * @Prezent\Translatable(targetEntity="Com\Nairus\ResumeBundle\Entity\Experience")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ExperienceTranslation
     */
    public function setDescription(string $description): ExperienceTranslation {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntityInterface $object): void {
        if (!$object instanceof Experience) {
            throw new \TypeError("Instance of [Experience] expected!");
        }
    }

}
