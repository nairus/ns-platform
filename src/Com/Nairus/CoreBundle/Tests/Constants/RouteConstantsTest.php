<?php

namespace Com\Nairus\CoreBundle\Constants;

use PHPUnit\Framework\TestCase;

/**
 * Test of RouteConstants.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class RouteConstantsTest extends TestCase {

    /**
     * Test the instanciation of the constants class.
     * The constructor has to be private and a PHP Error is expected.
     *
     * @expectedException \Error
     *
     * @return void
     */
    public function testContructor(): void {
        new RouteConstants();
    }

}
