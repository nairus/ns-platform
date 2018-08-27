<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        $this->object = new Education();
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
     * @expectedExceptionMessage Instance of [EducationTranslation] expected!
     */
    public function testAddBadTranslation() {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [EducationTranslation] expected!
     */
    public function testRemoveBadTranslation() {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity("en", "description", "bad translation"));
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setInstitution
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getInstitution
     */
    public function testGetAndSetInstitution() {
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
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setInstitution
     *
     * @expectedException \TypeError
     */
    public function testSetInstitutionWithNullParam() {
        $this->object->setInstitution(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDiploma
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getDiploma
     */
    public function testGetAndSetDiploma() {
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
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDiploma
     *
     * @expectedException \TypeError
     */
    public function testSetDiplomaWithNullParam() {
        $this->object->setDiploma(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDomain
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getDomain
     */
    public function testGetAndSetDomain() {
        try {
            $this->object->setDomain("Web");
            $this->assertSame("Web", $this->object->getDomain());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDomain
     *
     * @expectedException \TypeError
     */
    public function testSetDomainWithNullParam() {
        $this->object->setDomain(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setStartYear
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getStartYear
     */
    public function testGetAndSetStartYear() {
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
     */
    public function testSetStartYearWithNullParam() {
        $this->object->setStartYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setEndYear
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getEndYear
     */
    public function testGetAndSetEndYear() {
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
     */
    public function testSetEndYearWithNullParam() {
        $this->object->setEndYear(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDescription
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getDescription
     */
    public function testGetAndSetDescription() {
        try {
            $desc = "Lorem ipsum ...";
            $this->object->setDescription($desc);
            $this->assertSame($desc, $this->object->getDescription());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown: " . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setDescription
     *
     * @expectedException \TypeError
     */
    public function testSetDescriptionWithNullParam() {
        $this->object->setDescription(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Education::setResume
     * @covers Com\Nairus\ResumeBundle\Entity\Education::getResume
     */
    public function testGetAndSetResume() {
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
