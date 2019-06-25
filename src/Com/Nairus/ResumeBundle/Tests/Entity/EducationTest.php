<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints as SFConstaints;

/**
 * Test for Education entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationTest extends KernelTestCase {

    /**
     * @var Education
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new Education();
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
     * @return void
     */
    public function testImplementation(): void {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity", $this->object, "1. The entity musts be of type [AbstractTranslatableEntity]");
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface", $this->object, "2. The entity musts implement [TranslatableEntityInterface] interface");
    }

    /**
     * Test adding bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [EducationTranslation] expected!
     *
     * @return void
     */
    public function testAddBadTranslation(): void {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [EducationTranslation] expected!
     *
     * @return void
     */
    public function testRemoveBadTranslation(): void {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test the getter/setter of institution property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setInstitution
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getInstitution
     *
     * @return void
     */
    public function testGetAndSetInstitution(): void {
        try {
            $this->object->setInstitution("AFPA");
            $this->assertSame("AFPA", $this->object->getInstitution());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDiploma
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getDiploma
     *
     * @return void
     */
    public function testGetAndSetDiploma(): void {
        try {
            $this->object->setDiploma("BTS");
            $this->assertSame("BTS", $this->object->getDiploma());
        } catch (\Exception $exc) {
            $this->fail("No error has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setStartYear
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getStartYear
     *
     * @return void
     */
    public function testGetAndSetStartYear(): void {
        try {
            $this->object->setStartYear(2017);
            $this->assertSame(2017, $this->object->getStartYear());

            // Tests PHP 7 : typecating int
            $this->object->setStartYear(true); // typecasting en (int)1
            $this->object->setStartYear(false); // typecasting en (int)0
            $this->object->setStartYear("42"); // typecasting en (int)42
            $this->object->setStartYear(1.5); // typecasting en (int)1
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setStartYear
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetStartYearWithNullParam(): void {
        $this->object->setStartYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setEndYear
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getEndYear
     *
     * @return void
     */
    public function testGetAndSetEndYear(): void {
        try {
            $this->object->setEndYear(2018);
            $this->assertSame(2018, $this->object->getEndYear());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setEndYear
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetEndYearWithNullParam(): void {
        $this->object->setEndYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setResume
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getResume
     *
     * @return void
     */
    public function testGetAndSetResume(): void {
        try {
            $resume = new Resume();
            $this->object->setResume($resume);
            $this->assertSame($resume, $this->object->getResume());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * Test the `getTranslationEntityClass` static method.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getTranslationEntityClass
     *
     * @return void
     */
    public function testGetTranslationEntityClass(): void {
        $this->assertSame(Translation\EducationTranslation::class, Education::getTranslationEntityClass(), "1. The translation class expected is not ok.");
    }

    /**
     * Test the validation of the entity and its translations.
     *
     * @return void
     */
    public function testValidationWithTranslationsCase1(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        // Test entity with translations.
        $this->object
                ->setCurrentLocale("fr")
                ->setDescription("")
                ->setDiploma("")
                ->setDomain("")
                ->setInstitution("")
        ;

        $errors = $validator->validate($this->object);

        $this->assertCount(6, $errors, "1.1 6 errors are expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 The error has to be a NotNull constraint.");

        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error2->getConstraint(), "1.3 The error has to be a NotNull constraint.");

        /* @var $error3 \Symfony\Component\Validator\ConstraintViolation */
        $error3 = $errors[2];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error3->getConstraint(), "1.4 The error has to be a NotNull constraint.");

        /* @var $error4 \Symfony\Component\Validator\ConstraintViolation */
        $error4 = $errors[3];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error4->getConstraint(), "1.5 The error has to be a NotBlank constraint.");

        /* @var $error5 \Symfony\Component\Validator\ConstraintViolation */
        $error5 = $errors[4];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error5->getConstraint(), "1.6 The error has to be a NotNull constraint.");

        /* @var $error4 \Symfony\Component\Validator\ConstraintViolation */
        $error6 = $errors[5];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error6->getConstraint(), "1.7 The error has to be a NotBlank constraint.");
    }

    /**
     * Test the Expression and Length constaints validation.
     *
     * @return void
     */
    public function testValidationWithTranslationsCase2(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $this->object
                ->setCurrentLocale("fr")
                ->setDescription("Description")
                ->setDiploma("Diploma")
                ->setDomain("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam luctus tincidunt elit vel cras amet bad.")
                ->setEndYear(2017)
                ->setInstitution("Institution")
                ->setStartYear(2018)
        ;

        $errors = $validator->validate($this->object);

        $this->assertCount(2, $errors, "1.1 2 errors are expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Expression::class, $error1->getConstraint(), "1.2 The error has to be a Length constraint.");

        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];
        $this->assertInstanceOf(SFConstaints\Length::class, $error2->getConstraint(), "1.3 The error has to be a Expression constraint.");
    }

}
