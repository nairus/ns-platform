<?php

namespace Com\Nairus\ResumeBundle\Constants;

use PHPUnit\Framework\TestCase;

/**
 * Test of ExceptionCodeConstants.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExceptionCodeConstantsTest extends TestCase {

    /**
     * Test the instanciation of the constants class.
     * The constructor has to be private and a PHP Error is expected.
     *
     * @expectedException \Error
     *
     * @return void
     */
    public function testContructor(): void {
        new ExceptionCodeConstants();
    }

}
