<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\CoreBundle\Entity\TranslatableEntity;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util as Sluggable;

/**
 * ResumeTranslation entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_resume_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ns_resume_translations_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class ResumeTranslation extends AbstractTranslationEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Resume", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntity $object): void {
        if (!$object instanceof Resume) {
            throw new \TypeError("Instance of [Resume] expected!");
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void {
        $this->generateSlug();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void {
        $this->generateSlug();
    }

    /**
     * Generate slug for translation.
     *
     * @return void
     */
    private function generateSlug(): void {
        // Ensure the slug generation for translation.
        if ("slug" === $this->getField()) {
            $slug = Sluggable\Urlizer::urlize($this->getContent());
            $this->setContent($slug);
        }
    }

}
