<?php

namespace Com\Nairus\CoreBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Manage News entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Find the news and dependencies for the current page.
     *
     * @param int $offset The beginning of the collection.
     * @param int $limit  The limit of entities per page.
     *
     * @return Paginator
     */
    public function findNewsForPage(int $offset, int $limit): Paginator {
        $qb = $this->getQueryBuilder();
        $qb->leftJoin("n.contents", "c")
                ->addSelect("c")
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * Retourne la query doctrine.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder() {
        return $this->createQueryBuilder("n")
                        ->orderBy("n.createdAt", "DESC");
    }

}
