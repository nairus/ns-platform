<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        $this->object = new Experience();
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
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartMonth
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetStartMonthWithNullParam(): void {
        $this->object->setStartMonth(null);
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
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndMonth
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetEndMonthWithNullParam(): void {
        $this->object->setEndMonth(null);
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
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartYear
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetStartYearWithNullParam(): void {
        $this->object->setStartYear(null);
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
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndYear
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetEndYearWithNullParam(): void {
        $this->object->setEndYear(null);
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

}
