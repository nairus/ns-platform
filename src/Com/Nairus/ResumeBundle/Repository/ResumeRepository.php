<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ResumeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResumeRepository extends \Doctrine\ORM\EntityRepository {

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
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getOnlineQueryBuilder(): \Doctrine\ORM\QueryBuilder {
        return $this->createQueryBuilder("r")
                        ->innerJoin("r.educations", "edu")
                        ->addSelect("edu")
                        ->innerJoin("r.experiences", "exp")
                        ->addSelect("exp")
                        ->innerJoin("r.resumeSkills", "rsk")
                        ->addSelect("rsk")
                        ->where("r.status = :status")
                        ->orderBy("r.createdAt", "DESC")
                        ->setParameter("status", ResumeStatusEnum::ONLINE);
    }

}
