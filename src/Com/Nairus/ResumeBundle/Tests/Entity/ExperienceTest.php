<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints as SFConstaints;

/**
 * Test of Experience entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceTest extends KernelTestCase {

    /**
     * @var Experience
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new Experience();
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
     * @expectedExceptionMessage Instance of [ExperienceTranslation] expected!
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
     * @expectedExceptionMessage Instance of [ExperienceTranslation] expected!
     *
     * @return void
     */
    public function testRemoveBadTranslation(): void {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test the getter/setter of the company property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCompany
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getCompany
     *
     * @return void
     */
    public function testGetAndSetCompany(): void {
        try {
            $this->object->setCompany("Nairus");
            $this->assertSame("Nairus", $this->object->getCompany());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCompany
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetCompanyWithNullParam(): void {
        $this->object->setCompany(null);
    }

    /**
     * Test the getter/setter of the location property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setLocation
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getLocation
     *
     * @return void
     */
    public function testGetAndSetLocation(): void {
        try {
            $this->object->setLocation("Marseille");
            $this->assertSame("Marseille", $this->object->getLocation());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setLocation
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetLocationWithNullParam() {
        $this->object->setLocation(null);
    }

    /**
     * Test the getter/setter of startMonth property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartMonth
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getStartMonth
     *
     * @return void
     */
    public function testGetAndSetSetStartMonth(): void {
        try {
            $this->object->setStartMonth(1);
            $this->assertSame(1, $this->object->getStartMonth());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndMonth
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getEndMonth
     *
     * @return void
     */
    public function testGetAndSetSetEndMonth(): void {
        try {
            $this->object->setEndMonth(12);
            $this->assertSame(12, $this->object->getEndMonth());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartYear
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getStartYear
     *
     * @return void
     */
    public function testGetAndSetSetStartYear(): void {
        try {
            $this->object->setStartYear(2017);
            $this->assertSame(2017, $this->object->getStartYear());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndYear
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getEndYear
     *
     * @return void
     */
    public function testGetAndSetSetEndYear(): void {
        try {
            $this->object->setEndYear(2017);
            $this->assertSame(2017, $this->object->getEndYear());
        } catch (\Throwable $exc) {
            $this->fail("No exception or error has to be thrown: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCurrentJob
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getCurrentJob
     *
     * @return void
     */
    public function testGetAndSetCurrentJob(): void {
        try {
            $this->assertSame(false, $this->object->getCurrentJob());
            $this->object->setCurrentJob(true);
            $this->assertSame(true, $this->object->getCurrentJob());
        } catch (\Throwable $exc) {
            $this->fail("No exception or error has to be thrown: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCurrentJob
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetCurrentJobWithNullParam(): void {
        $this->object->setCurrentJob(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setResume
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getResume
     *
     * @return void
     */
    public function testGetAndSetSetResume(): void {
        try {
            $resume = new Resume();
            $this->object->setResume($resume);
            $this->assertSame($resume, $this->object->getResume());
        } catch (\Throwable $exc) {
            $this->fail("No exception or error has to be thrown: " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>getTranslationEntityClass</code> static method.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getTranslationEntityClass
     *
     * @return void
     */
    public function testGetTranslationEntityClass(): void {
        $this->assertSame(Translation\ExperienceTranslation::class, Experience::getTranslationEntityClass(), "1. The translation class expected is not ok.");
    }

    /**
     * Test the validation of the entity.
     *
     * @return void
     */
    public function testValidationWithTranslationsCase1(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $this->object->setCurrentLocale('fr')
                ->setCurrentJob(true)
                ->setCompany("")
                ->setDescription("")
                ->setLocation("");

        $errors = $validator->validate($this->object);

        $this->assertCount(5, $errors, "1. Five error messages are expected.");

        $num = 2;
        $errorFieldsExpected = ["company", "location", "startMonth", "startYear", "translations[fr].description"];
        foreach ($errors as /* @var $error \Symfony\Component\Validator\ConstraintViolation */ $error) {
            $this->assertInstanceOf(SFConstaints\NotBlank::class, $error->getConstraint(), "$num.1 The constraint expected is not valid.");
            $this->assertContains($error->getPropertyPath(), $errorFieldsExpected, "$num.2 The field expected is not ok.");
            $num ++;
        }
    }

    /**
     * Test the validation of the entity.
     *
     * @return void
     */
    public function testValidationWithTranslationsCase2(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $this->object->setCurrentLocale('fr')
                ->setCurrentJob(false)
                ->setCompany("Company")
                ->setDescription("Description")
                ->setLocation("Location")
                ->setStartMonth(10)
                ->setStartYear(2005)
                ->setEndMonth("")
                ->setEndYear("");

        $errors = $validator->validate($this->object);

        $this->assertCount(2, $errors, "1. Two error messages are expected.");

        $num = 2;
        $errorFieldsExpected = ["endYear", "currentJob"];
        foreach ($errors as /* @var $error \Symfony\Component\Validator\ConstraintViolation */ $error) {
            $this->assertInstanceOf(SFConstaints\Expression::class, $error->getConstraint(), "2.$num The constraint expected is not valid.");
            $this->assertContains($error->getPropertyPath(), $errorFieldsExpected, "$num.2 The field expected is not ok.");
            $num ++;
        }
    }

    /**
     * Test the validation of the entity.
     *
     * @return void
     */
    public function testValidationWithTranslationsCase3(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $this->object->setCurrentLocale('fr')
                ->setCurrentJob(false)
                ->setCompany("Company")
                ->setDescription("Description")
                ->setLocation("Location")
                ->setStartMonth(10)
                ->setStartYear(2005)
                ->setEndMonth(9)
                ->setEndYear(2005);

        $errors = $validator->validate($this->object);

        $this->assertCount(1, $errors, "1. One error message is expected.");

        /* @var $error \Symfony\Component\Validator\ConstraintViolation */
        $error = $errors[0];
        $this->assertInstanceOf(SFConstaints\Expression::class, $error->getConstraint(), "2.1 The constraint expected is not valid.");
        $this->assertEquals("endMonth", $error->getPropertyPath(), "2.2 The field expected is not ok.");
    }

}
