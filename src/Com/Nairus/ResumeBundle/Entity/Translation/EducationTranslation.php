<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\Education;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * EducationTranslation entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_education_translations")
 */
class EducationTranslation extends AbstractTranslationEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Prezent\Translatable(targetEntity="Com\Nairus\ResumeBundle\Entity\Education")
     */
    protected $translatable;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Education
     */
    public function setDescription(string $description): EducationTranslation {
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
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntityInterface $object): void {
        if (!$object instanceof Education) {
            throw new \TypeError("Instance of [Education] expected!");
        }
    }

}
