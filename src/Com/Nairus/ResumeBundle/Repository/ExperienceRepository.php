<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Collection\ExperienceCollection;

/**
 * Repository for experience entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceRepository extends \Doctrine\ORM\EntityRepository {

    private static $entityClass = Experience::class;

    /**
     * Find experiences for a resume, ordered by start month and year.
     *
     * @param int    $resumeId The current resume id.
     * @param string $locale   The current locale.
     *
     * @return ExperienceCollection
     */
    public function findOrderedForResumeId(int $resumeId, string $locale): ExperienceCollection {
        $dql = "SELECT ex, extrans
                FROM " . static::$entityClass . " ex
                JOIN ex.translations extrans WITH extrans.locale = :locale
                JOIN ex.resume r
                WHERE
                    r.id = :resumeId
                ORDER BY ex.startYear DESC, ex.startMonth DESC";

        $experiences = $this->getEntityManager()
                ->createQuery($dql)
                ->setParameters(['resumeId' => $resumeId, 'locale' => $locale])
                ->getResult();

        return new ExperienceCollection($experiences);
    }

}
