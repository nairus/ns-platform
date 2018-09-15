<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * ResumeTranslation entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_resume_translations")
 */
class ResumeTranslation extends AbstractTranslationEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=100, unique=true)
     */
    private $slug;

    /**
     * @Prezent\Translatable(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume")
     */
    protected $translatable;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Resume
     */
    public function setTitle(string $title): self {
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug): self {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug(): string {
        return $this->slug;
    }

    /**
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntityInterface $object): void {
        if (!$object instanceof Resume) {
            throw new \TypeError("Instance of [Resume] expected!");
        }
    }

}
