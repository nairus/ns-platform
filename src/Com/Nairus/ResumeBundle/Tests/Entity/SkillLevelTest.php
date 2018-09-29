<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Test for SkillLevel entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelTest extends AbstractKernelTestCase {

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
        parent::tearDown();
        unset($this->object);
    }

    /**
     * Test the implementation of the entity.
     *
     */
    public function testImplementation() {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity", $this->object, "1. The entity musts be of type [AbstractTranslatableEntity]");
    }

    /**
     * Test if the entity has the IsNew Trait.
     *
     * @covers \Com\Nairus\ResumeBundle\Entity\SkillLevel::isNew
     */
    public function testIsNew() {
        $this->assertTrue($this->object->isNew(), "1. The entity has to be new.");
    }

    /**
     * Test adding bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [SkillLevelTranslation] expected!
     */
    public function testAddBadTranslation() {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [SkillLevelTranslation] expected!
     */
    public function testRemoveBadTranslation() {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test the <code>getTranslationEntityClass</code> static method.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\SkillLevel::getTranslationEntityClass
     *
     * @return void
     */
    public function testGetTranslationEntityClass(): void {
        $this->assertSame(Translation\SkillLevelTranslation::class, SkillLevel::getTranslationEntityClass(), "1. The translation class expected is not ok.");
    }

    /**
     * Test the validation of the entity with his translation.
     *
     * @return void
     */
    public function testValidationWithTranslation(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        $skillLevel = new SkillLevel();
        $skillLevel->translate("fr")->setTitle("Bad");
        $skillLevel->translate("en")->setTitle("");
        $skillLevel->translate("ru")->setTitle("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla tempus, nulla a congue viverra metus. Too long!");

        $errors = $validator->validate($skillLevel);
        $this->assertCount(3, $errors, "1.1. 3 errors is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];
        /* @var $error3 \Symfony\Component\Validator\ConstraintViolation */
        $error3 = $errors[2];

        $this->assertInstanceOf(Length::class, $error1->getConstraint(), "1.2. The first error has to be a Length constraint.");
        $this->assertInstanceOf(NotBlank::class, $error2->getConstraint(), "1.3. The second error has to be a NotNull constraint.");
        $this->assertInstanceOf(Length::class, $error3->getConstraint(), "1.4. The third error has to be a Length constraint.");
    }

}
