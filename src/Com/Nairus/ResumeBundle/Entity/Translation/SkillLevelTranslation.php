<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Com\Nairus\CoreBundle\Entity\TranslatableEntity;
use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Doctrine\ORM\Mapping as ORM;

/**
 * SkillLevelTranslation Entity
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Entity
 * @ORM\Table(name="ns_skill_level_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ns_skill_level_translations_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class SkillLevelTranslation extends AbstractTranslationEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Com\Nairus\ResumeBundle\Entity\SkillLevel", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * {@ihneritDoc}
     */
    protected function validObjectClass(TranslatableEntity $object): void {
        if (!$object instanceof SkillLevel) {
            throw new \TypeError("Instance of [SkillLevel] expected!");
        }
    }

}
