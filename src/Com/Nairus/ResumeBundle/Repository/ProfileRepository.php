<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\UserBundle\Entity\User;

/**
 * Repository for Profile entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ProfileRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Get the user profile with avatar.
     *
     * @param User $user The user
     *
     * @return Profile
     */
    public function getWithAvatarForUser(User $user): Profile {
        $dql = "SELECT p, a
                FROM " . Profile::class . " p
                INNER JOIN p.user u
                LEFT JOIN p.avatar a
                WHERE u.id = :userId";

        return $this->getEntityManager()->createQuery($dql)
                        ->setParameter("userId", $user->getId())
                        ->getSingleResult();
    }

}
