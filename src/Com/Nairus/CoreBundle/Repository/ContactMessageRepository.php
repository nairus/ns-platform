<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\Entity\IpTraceable;
use Com\Nairus\CoreBundle\Validator\Antifloodable;
use Com\Nairus\CoreBundle\Entity\ContactMessage;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ContactMessage repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageRepository extends EntityRepository implements Antifloodable {

    private static $entityClass = ContactMessage::class;

    /**
     * {@inheritDoc}
     */
    public function isFlood(IpTraceable $entity, int $seconds): bool {
        $requestDate = new \DateTimeImmutable("$seconds seconds ago");
        $dql = "SELECT COUNT(cm) FROM " . static::$entityClass . " cm " .
                "WHERE cm.ip = :ip AND cm.requestDate >= :requestDate";

        $nbMessage = $this->getEntityManager()->createQuery($dql)
                ->setParameter("ip", $entity->getIp())
                ->setParameter("requestDate", $requestDate)
                ->setMaxResults(1)
                ->getSingleScalarResult();

        return $nbMessage > 0;
    }

    /**
     * Find contact message for current page.
     *
     * @param int $offset The beginning of the collection.
     * @param int $limit  The limit of entities per page.
     *
     * @return Paginator
     */
    public function findAllForPage(int $offset, int $limit): Paginator {
        $qb = $this->createQueryBuilder("cm");
        $qb->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy("cm.requestDate", "DESC");

        return new Paginator($qb->getQuery());
    }

}
