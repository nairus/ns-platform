<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntity;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\Education;
use Doctrine\ORM\Mapping as ORM;

/**
 * EducationTranslation entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_education_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ns_education_translations_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class EducationTranslation extends AbstractTranslationEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Education", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    public function validObjectClass(TranslatableEntity $object): void {
        if (!$object instanceof Education) {
            throw new \TypeError("Instance of [Education] expected!");
        }
    }

}
