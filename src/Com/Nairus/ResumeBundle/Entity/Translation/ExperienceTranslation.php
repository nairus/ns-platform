<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntity;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Doctrine\ORM\Mapping as ORM;

/**
 * ExperienceTransalation Entity
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_experience_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ns_experience_translations_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ExperienceTranslation extends AbstractTranslationEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\Experience", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    protected function validObjectClass(TranslatableEntity $object): void {
        if (!$object instanceof Experience) {
            throw new \TypeError("Instance of [Experience] expected!");
        }
    }

}
