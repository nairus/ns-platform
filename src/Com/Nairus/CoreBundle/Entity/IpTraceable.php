<?php

namespace Com\Nairus\CoreBundle\Entity;

/**
 * Entity ip traceable.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface IpTraceable {

    /**
     * Return the ip of the entity.
     *
     * @return string|null
     */
    public function getIp(): ?string;
}
