<?php

namespace Com\Nairus\CoreBundle\Repository;

/**
 * Manage NewsContent entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsContentRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Find the last news published
     *
     * @param int    $limit    The limit of news to find.
     * @param string $language The language of the news.
     *
     * @return Collection <NewsContent>
     */
    public function findLastNewsPublished(int $limit, string $language) {
        $qb = $this->createQueryBuilder("nc")
                ->innerJoin("nc.news", "n")
                ->where("n.published = 1")
                ->andWhere("nc.locale = :language")
                ->orderBy("n.publishedAt", "DESC")
                ->setMaxResults($limit)
                ->setParameter("language", $language);

        return $qb->getQuery()->execute();
    }

}
