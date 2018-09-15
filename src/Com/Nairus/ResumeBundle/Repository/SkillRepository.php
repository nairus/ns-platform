<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Skill;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Skill repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Find the skills for the current page.
     *
     * @param int $offset The beginning of the collection.
     * @param int $limit  The limit of entities per page.
     *
     * @return Paginator
     */
    public function findAllForPage(int $offset, int $limit): Paginator {
        $qb = $this->createQueryBuilder("s");
        $qb->setFirstResult($offset)
                ->setMaxResults($limit);

        return new Paginator($qb);
    }

    /**
     * Remove the entity.
     *
     * @param Skill $skill The entity to remove.
     *
     * @return void
     */
    public function remove(Skill $skill): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($skill);
        $entityManager->flush($skill);
    }

}
