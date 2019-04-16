<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection;

/**
 * ResumeSkill repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillRepository extends \Doctrine\ORM\EntityRepository {

    private static $entityClass = ResumeSkill::class;

    /**
     * Return the resume skills order by rank.
     *
     * @param int    $resumeId The resume id.
     * @param string $locale   The current locale.
     *
     * @return ResumeSkillCollection
     */
    public function findOrderedByRank(int $resumeId, string $locale): ResumeSkillCollection {
        $dql = "SELECT rs, s, sl, slt
                FROM " . static::$entityClass . " rs
                JOIN rs.resume r
                JOIN rs.skill s
                JOIN rs.skillLevel sl
                JOIN sl.translations slt WITH slt.locale = :locale
                WHERE
                    r.id = :resumeId
                ORDER BY rs.rank ASC";

        $resumeSkills = $this->getEntityManager()
                ->createQuery($dql)
                ->setParameters(['resumeId' => $resumeId, 'locale' => $locale])
                ->getResult();

        return new ResumeSkillCollection($resumeSkills);
    }

}
