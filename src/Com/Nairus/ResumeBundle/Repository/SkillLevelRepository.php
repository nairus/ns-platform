<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\SkillLevel;

/**
 * SkillLevel repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Remove the skill level entity.
     *
     * @param SkillLevel $skillLevel The entity to remove.
     *
     * @return void
     */
    public function remove(SkillLevel $skillLevel): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($skillLevel);
        $entityManager->flush($skillLevel);
    }

}
