<?php

namespace Com\Nairus\ResumeBundle\Exception;

use PHPUnit\Framework\TestCase;

/**
 * Test of ResumeIncompleteException.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeIncompleteExceptionTest extends TestCase {

    /**
     * Test the constructor.
     *
     * @return void
     */
    public function testConstructor(): void {
        $this->assertInstanceOf(ResumePublicationException::class, new ResumeIncompleteException("key.error"), "1. The exception has to extends [ResumePublicationException].");
    }

}
