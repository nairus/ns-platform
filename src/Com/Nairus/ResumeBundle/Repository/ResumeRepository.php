<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository for Resume entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeRepository extends \Doctrine\ORM\EntityRepository {

    private static $entityClass = Resume::class;

    /**
     * Return the list of Resume for the current page.
     *
     * @param int $page      The current page.
     * @param int $nbPerPage The number of entity per page.
     *
     * @return Paginator
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage): Paginator {
        $qb = $this->getOnlineQueryBuilder();
        $qb
                ->setFirstResult(($page - 1) * $nbPerPage)
                ->setMaxResults($nbPerPage);

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * Find a resume with a translation.
     *
     * This query's purpose is only for testing translatable behavior.
     *
     * @return Resume
     */
    public function findWithTranslation(int $resumeId, string $locale): ?Resume {
        $dql = "SELECT resume
                FROM " . static::$entityClass . " resume
                JOIN resume.translations trans
                WHERE
                    resume.id = :id
                    AND trans.locale = :locale";
        $resume = $this->getEntityManager()
                ->createQuery($dql)
                ->setParameters(["id" => $resumeId, "locale" => $locale])
                ->getResult();

        if (count($resume) == 0) {
            return null;
        }

        return $resume[0];
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getOnlineQueryBuilder(): \Doctrine\ORM\QueryBuilder {
        return $this->createQueryBuilder("r")
                        ->leftJoin("r.translations", "rtrans")
                        ->addSelect("rtrans")
                        ->innerJoin("r.educations", "edu")
                        ->addSelect("edu")
                        ->leftJoin("edu.translations", "edutrans")
                        ->addSelect("edutrans")
                        ->innerJoin("r.experiences", "exp")
                        ->addSelect("exp")
                        ->leftJoin("exp.translations", "exptrans")
                        ->addSelect("exptrans")
                        ->innerJoin("r.resumeSkills", "rsk")
                        ->addSelect("rsk")
                        ->where("r.status = :status")
                        ->groupBy("r.id")
                        ->orderBy("r.createdAt", "DESC")
                        ->setParameter("status", ResumeStatusEnum::ONLINE);
    }

}
