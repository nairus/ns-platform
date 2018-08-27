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
     */
    public function testImplementation() {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity", $this->object, "1. The entity musts be of type [AbstractTranslatableEntity]");
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslatableEntity", $this->object, "2. The entity musts implement [TranslatableEntity] interface");
    }

    /**
     * Test adding bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [ExperienceTranslation] expected!
     */
    public function testAddBadTranslation() {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [ExperienceTranslation] expected!
     */
    public function testRemoveBadTranslation() {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCompany
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getCompany
     */
    public function testGetAndSetCompany() {
        try {
            $this->object->setCompany("Nairus");
            $this->assertSame("Nairus", $this->object->getCompany());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCompany
     *
     * @expectedException \TypeError
     */
    public function testSetCompanyWithNullParam() {
        $this->object->setCompany(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setLocation
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getLocation
     */
    public function testGetAndSetLocation() {
        try {
            $this->object->setLocation("Marseille");
            $this->assertSame("Marseille", $this->object->getLocation());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setLocation
     *
     * @expectedException \TypeError
     */
    public function testSetLocationWithNullParam() {
        $this->object->setLocation(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setDescription
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getDescription
     */
    public function testGetAndSetSetDescription() {
        try {
            $this->object->setDescription("Description");
            $this->assertSame("Description", $this->object->getDescription());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setDescription
     *
     * @expectedException \TypeError
     */
    public function testSetDescriptionWithNullParam() {
        $this->object->setDescription(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartMonth
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getStartMonth
     */
    public function testGetAndSetSetStartMonth() {
        try {
            $this->object->setStartMonth(1);
            $this->assertSame(1, $this->object->getStartMonth());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartMonth
     *
     * @expectedException \TypeError
     */
    public function testSetStartMonthWithNullParam() {
        $this->object->setStartMonth(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndMonth
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getEndMonth
     */
    public function testGetAndSetSetEndMonth() {
        try {
            $this->object->setEndMonth(12);
            $this->assertSame(12, $this->object->getEndMonth());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndMonth
     *
     * @expectedException \TypeError
     */
    public function testSetEndMonthWithNullParam() {
        $this->object->setEndMonth(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartYear
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getStartYear
     */
    public function testGetAndSetSetStartYear() {
        try {
            $this->object->setStartYear(2017);
            $this->assertSame(2017, $this->object->getStartYear());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setStartYear
     *
     * @expectedException \TypeError
     */
    public function testSetStartYearWithNullParam() {
        $this->object->setStartYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndYear
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getEndYear
     */
    public function testGetAndSetSetEndYear() {
        try {
            $this->object->setEndYear(2017);
            $this->assertSame(2017, $this->object->getEndYear());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setEndYear
     *
     * @expectedException \TypeError
     */
    public function testSetEndYearWithNullParam() {
        $this->object->setEndYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCurrentJob
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getCurrentJob
     */
    public function testGetAndSetCurrentJob() {
        try {
            $this->assertSame(false, $this->object->getCurrentJob());
            $this->object->setCurrentJob(true);
            $this->assertSame(true, $this->object->getCurrentJob());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setCurrentJob
     *
     * @expectedException \TypeError
     */
    public function testSetCurrentJobWithNullParam() {
        $this->object->setCurrentJob(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::setResume
     * @covers Com\Nairus\ResumeBundle\Entity\Experience::getResume
     */
    public function testGetAndSetSetResume() {
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

}
