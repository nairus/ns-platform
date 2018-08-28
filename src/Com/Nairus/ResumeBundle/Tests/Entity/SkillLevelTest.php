<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test for SkillLevel entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelTest extends KernelTestCase {

    /**
     * @var SkillLevel
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new SkillLevel();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        unset($this->object);
    }

    /**
     * Test the implementation of the entity.
     */
    public function testImplementation() {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity", $this->object, "1. The entity musts be of type [AbstractTranslatableEntity]");
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslatableEntity", $this->object, "2. The entity musts implement [TranslatableEntity] interface");
    }

    /**
     * Test adding bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [SkillLevelTranslation] expected!
     */
    public function testAddBadTranslation() {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [SkillLevelTranslation] expected!
     */
    public function testRemoveBadTranslation() {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
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
            $this->fail("Aucune exception ne doit être levée :" . $exc->getMessage());
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

}
