<?php

namespace Com\Nairus\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;

/**
 * Test of Antiflood.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AntifloodTest extends TestCase {

    /**
     * Test the `getRequiredOptions` method.
     *
     * @return void
     */
    public function testGetRequiredOptions(): void {
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $this->assertContains(Antiflood::OPTION_SECONDS, $constraint->getRequiredOptions());
    }

    /**
     * Test the `validatedBy`.
     *
     * @return void
     */
    public function testValidatedBy(): void {
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $this->assertEquals("ns_core.validator.antiflood", $constraint->validatedBy());
    }

    /**
     * Test the `getTargets` method.
     *
     * @return void
     */
    public function testGetTargets(): void {
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $this->assertEquals(Antiflood::CLASS_CONSTRAINT, $constraint->getTargets());
    }

    /**
     * Test the `getDefaultOption` method.
     *
     * @return void
     */
    public function testGetDefaultOption(): void {
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $this->assertEquals(Antiflood::OPTION_SECONDS, $constraint->getDefaultOption());
    }

}
