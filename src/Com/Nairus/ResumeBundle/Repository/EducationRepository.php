<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Collection\EducationCollection;
use Com\Nairus\ResumeBundle\Entity\Education;

/**
 * Repository for Education entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationRepository extends \Doctrine\ORM\EntityRepository {

    private static $entityClass = Education::class;

    /**
     * Find educations for a resume, ordered by start year.
     *
     * @param int    $resumeId The current resume id.
     * @param string $locale   The current locale.
     *
     * @return EducationCollection
     */
    public function findOrderedForResumeId(int $resumeId, string $locale): EducationCollection {
        $dql = "SELECT ed, edtrans
                FROM " . static::$entityClass . " ed
                JOIN ed.translations edtrans WITH edtrans.locale = :locale
                JOIN ed.resume r
                WHERE
                    r.id = :resumeId
                ORDER BY ed.startYear DESC";

        $educations = $this->getEntityManager()
                ->createQuery($dql)
                ->setParameters(['resumeId' => $resumeId, 'locale' => $locale])
                ->getResult();

        return new EducationCollection($educations);
    }

}
