<?php

namespace Com\Nairus\CoreBundle\Repository;

/**
 * Manage NewsContent entities.
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
