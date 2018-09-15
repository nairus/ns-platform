<?php

namespace Com\Nairus\CoreBundle\Exception;

use PHPUnit\Framework\TestCase;

/**
 * Test of LocaleError.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LocaleErrorTest extends TestCase {

    /**
     * @var LocaleError
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new LocaleError("fr");
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        unset($this->object);
    }

    /**
     * Test the <code>getLocale</code> method.
     *
     * @covers \Com\Nairus\CoreBundle\Exception\LocaleError::getLocale
     * 
     * @return void
     */
    public function testGetLocale(): void {
        $this->assertSame("fr", $this->object->getLocale(), "1. The locale property expected is not ok.");
    }

}
