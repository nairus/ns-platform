<?php

namespace Com\Nairus\ResumeBundle\Exception;

use PHPUnit\Framework\TestCase;

/**
 * Test of ResumePublicationException.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumePublicationExceptionTest extends TestCase {

    /**
     * Test the constructor.
     *
     * @return void
     */
    public function testConstructor(): void {
        $this->assertInstanceOf(\Com\Nairus\CoreBundle\Exception\FunctionalException::class, new ResumePublicationException("key.error"),
                "1. The exception has to extends [FunctionalException].");
    }

}
