<?php

namespace Com\Nairus\CoreBundle\Form;

use PHPUnit\Framework\TestCase;

/**
 * Test of AntispamType.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AntispamTypeTest extends TestCase {

    /**
     * Test <code>getName</code> method.
     *
     * @return void
     */
    public function testGetName(): void {
        $antispamType = new AntispamType(["yes" => "yes"]);
        $this->assertEquals("antispam", $antispamType->getName());
    }

}
