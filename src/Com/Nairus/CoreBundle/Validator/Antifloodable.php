<?php

namespace Com\Nairus\CoreBundle\Validator;

use Com\Nairus\CoreBundle\Entity\IpTraceable;

/**
 * Interface of antiflood repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface Antifloodable {

    /**
     * Return <code>true</code> if we detect a client's flood of requests.
     *
     * @param string $clientIp The current client IP.
     * @param int    $seconds  Number of seconds allowed between two requests.
     *
     * @return boolean
     */
    public function isFlood(IpTraceable $entity, int $seconds): bool;
}
