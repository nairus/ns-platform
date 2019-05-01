<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\Expr as Expr;

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
     * @param int    $page      The current page.
     * @param int    $nbPerPage The number of entity per page.
     * @param string $locale    The current locale.
     *
     * @return Paginator
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage, string $locale): Paginator {
        $qb = $this->getOnlineQueryBuilder($locale);
        $qb
                ->setFirstResult(($page - 1) * $nbPerPage)
                ->setMaxResults($nbPerPage);

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * Find a resume with a translation and his author.
     *
     * @return Resume
     */
    public function findWithTranslationAndAuthor(int $resumeId, string $locale): ?Resume {
        $dql = "SELECT resume, trans, author
                FROM " . static::$entityClass . " resume
                JOIN resume.translations trans
                JOIN resume.author author
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
     * Build the query.
     *
     * @codeCoverageIgnore
     *
     * @param string $locale The current locale
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getOnlineQueryBuilder(string $locale): \Doctrine\ORM\QueryBuilder {
        $qb = $this->createQueryBuilder("r");

        return $qb->innerJoin("r.translations", "rtrans", Expr\Join::WITH, "rtrans.locale = :locale")
                        ->addSelect("rtrans")
                        ->innerJoin("r.educations", "edu")
                        ->addSelect("edu")
                        ->innerJoin("r.experiences", "exp")
                        ->addSelect("exp")
                        ->innerJoin("r.resumeSkills", "rsk")
                        ->addSelect("rsk")
                        ->innerJoin("rsk.skill", "ski")
                        ->addSelect("ski")
                        ->innerJoin("rsk.skillLevel", "skl")
                        ->addSelect("skl")
                        ->leftJoin("skl.translations", "skltrans", Expr\Join::WITH, "skltrans.locale = :locale")
                        ->addSelect("skltrans")
                        ->where("r.status = :status")
                        ->groupBy("r.id")
                        ->orderBy("r.createdAt", "DESC")
                        ->setParameter("status", ResumeStatusEnum::ONLINE)
                        ->setParameter("locale", $locale);
    }

}
