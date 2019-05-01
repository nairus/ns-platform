<?php

namespace Com\Nairus\CoreBundle\Exception;

use PHPUnit\Framework\TestCase;

/**
 * Test of GoneHttpException.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GoneHttpExceptionTest extends TestCase {

    /**
     * @var GoneHttpException
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new GoneHttpException("http://www.nairus.com/");
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        unset($this->object);
    }

    /**
     * Test the <code>getRedirectUrl</code> method.
     *
     * @return void
     */
    public function testGetRedirectUri(): void {
        $this->assertEquals("http://www.nairus.com/", $this->object->getRedirectUrl());
    }

}
