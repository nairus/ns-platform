<?php

namespace Com\Nairus\CoreBundle\Entity;

use PHPUnit\Framework\TestCase;

/**
 * Test of BlacklistedIp entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BlacklistedIpTest extends TestCase {

    /**
     * Test the `getBlacklistedAt` method.
     */
    public function testGetBlacklistedAt() {
        $blacklistedIp = new BlacklistedIp();
        $this->assertInstanceOf(\DateTimeImmutable::class, $blacklistedIp->getBlacklistedAt(), "1. The field has to be filled.");
        $blacklistedIp->setBlacklistedAt(new \DateTime());
        $this->assertInstanceOf(\DateTimeImmutable::class, $blacklistedIp->getBlacklistedAt(), "2. The field has to be a [DateTimeImmutable] instance.");
    }

}
