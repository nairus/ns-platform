<?php

namespace Com\Nairus\ResumeBundle\Enums;

use PHPUnit\Framework\TestCase;

/**
 * Test of ResumeStatusEnum.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeStatusEnumTest extends TestCase {

    /**
     * Test the instanciation of the constants class.
     * The constructor has to be private and a PHP Error is expected.
     *
     * @expectedException \Error
     *
     * @return void
     */
    public function testContructor(): void {
        new ResumeStatusEnum();
    }

    /**
     * Test the <code>getIconClass</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::getIconClass
     *
     * @return void
     */
    public function testGetIconClass(): void {
        $this->assertSame("fas fa-thermometer-quarter", ResumeStatusEnum::getIconClass(ResumeStatusEnum::OFFLINE_INCOMPLETE), "1. The icon class expected is not ok!");
        $this->assertSame("fas fa-thermometer-half", ResumeStatusEnum::getIconClass(ResumeStatusEnum::OFFLINE_TO_PUBLISHED), "2. The icon class expected is not ok!");
        $this->assertSame("fas fa-thermometer-full", ResumeStatusEnum::getIconClass(ResumeStatusEnum::ONLINE), "3. The icon class expected is not ok!");
    }

    /**
     * Test the <code>getIconClass</code> method with a bad status.
     *
     * @covers Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::getIconClass
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The resume status [999] does not exist!
     *
     * @return void
     */
    public function testGetIconClassWithBadStatus(): void {
        ResumeStatusEnum::getIconClass(999);
    }

    /**
     * Test the <code>getLabelKey</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::getLabelKey
     *
     * @return void
     */
    public function testGetLabelKey(): void {
        $this->assertSame("resume.status.offline", ResumeStatusEnum::getLabelKey(ResumeStatusEnum::OFFLINE_INCOMPLETE), "1. The icon class expected is not ok!");
        $this->assertSame("resume.status.to-published", ResumeStatusEnum::getLabelKey(ResumeStatusEnum::OFFLINE_TO_PUBLISHED), "2. The icon class expected is not ok!");
        $this->assertSame("resume.status.online", ResumeStatusEnum::getLabelKey(ResumeStatusEnum::ONLINE), "3. The icon class expected is not ok!");
    }

    /**
     * Test the <code>getLabelKey</code> method with a bad status.
     *
     * @covers Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::getLabelKey
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The resume status [999] does not exist!
     *
     * @return void
     */
    public function testGetLabelKeyBadStatus(): void {
        ResumeStatusEnum::getLabelKey(999);
    }

}
