<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests for Skill entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillTest extends KernelTestCase {

    /**
     * @var Skill
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Skill();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        unset($this->object);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\SkillLevel::setTitle
     * @covers Com\Nairus\ResumeBundle\Entity\SkillLevel::getTitle
     */
    public function testGetAndSetTitle() {
        try {
            $this->object->setTitle("Title");
            $this->assertSame("Title", $this->object->getTitle());
        } catch (\Exception $exc) {
            $this->fail("No excepection expected:" . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\SkillLevel::setTitle
     *
     * @expectedException \TypeError
     */
    public function testSetTitleWithNullParam() {
        $this->object->setTitle(null);
    }

    /**
     * Test isNew method.
     *
     * @covers \Com\Nairus\ResumeBundle\Entity\Skill::isNew
     */
    public function testIsNew(): void {
        $this->assertTrue($this->object->isNew(), "1. The entity has to be new.");
    }

}
