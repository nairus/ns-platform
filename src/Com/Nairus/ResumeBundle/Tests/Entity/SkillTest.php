<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Tests for Skill entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillTest extends AbstractKernelTestCase {

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
            $this->fail("No exception expected:" . $exc->getMessage());
        }
    }

    /**
     * Test isNew method.
     *
     * @covers \Com\Nairus\ResumeBundle\Entity\Skill::isNew
     */
    public function testIsNew(): void {
        $this->assertTrue($this->object->isNew(), "1. The entity has to be new.");
    }

    /**
     * Test the entity validation.
     *
     * @return void
     */
    public function testValidate(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        // Case 1.
        $skill = new Skill();
        $skill->setTitle("");
        $errors = $validator->validate($skill);
        $this->assertCount(1, $errors, "1.1 One error is expected.");
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint(), "1.2 The error has to be a NotNull constraint.");

        // Case 2.
        $skill->setTitle("a");
        $errors = $validator->validate($skill);
        $this->assertCount(1, $errors, "2.1 One error is expected.");
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint(), "2.2 The error has to be a Length constraint.");
    }

}
