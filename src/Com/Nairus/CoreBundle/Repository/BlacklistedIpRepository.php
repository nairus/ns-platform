<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\Entity\BlacklistedIp;

/**
 * BlacklistedIp repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BlacklistedIpRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Return `true` if the ip is blacklisted.
     *
     * @param string $ip The ip to find.
     *
     * @return bool
     */
    public function isBlackListed(string $ip): bool {
        $dql = "SELECT COUNT(b) FROM " . BlacklistedIp::class . " b WHERE b.ip = :ip";
        $result = $this->getEntityManager()->createQuery($dql)
                ->setParameter("ip", $ip)
                ->setMaxResults(1)
                ->getSingleScalarResult();

        return $result > 0;
    }

}
