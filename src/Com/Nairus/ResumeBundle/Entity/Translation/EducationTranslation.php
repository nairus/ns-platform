<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\Education;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 100
     * )
     */
    private $domain;

    /**
     * @Prezent\Translatable(targetEntity="Com\Nairus\ResumeBundle\Entity\Education")
     */
    protected $translatable;

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return EducationTranslation
     */
    public function setDescription(?string $description): EducationTranslation {
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
     * Set the resume domain.
     *
     * @param string|null $domain
     *
     * @return EducationTranslation
     */
    public function setDomain(?string $domain): EducationTranslation {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get the education domain.
     *
     * @return string|null
     */
    public function getDomain() {
        return $this->domain;
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
