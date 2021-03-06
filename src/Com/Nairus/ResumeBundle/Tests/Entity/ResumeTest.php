<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Test of Resume entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeTest extends KernelTestCase {

    /**
     * @var Resume
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new Resume();
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
     */
    public function testImplementation() {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity", $this->object, "1. The entity musts be of type [AbstractTranslatableEntity]");
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface", $this->object, "2. The entity musts implement [TranslatableEntityInterface] interface");
    }

    /**
     * Test adding bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [ResumeTranslation] expected!
     */
    public function testAddBadTranslation() {
        $this->object->addTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * Test removing bad translation.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [ResumeTranslation] expected!
     */
    public function testRemoveBadTranslation() {
        $this->object->removeTranslation(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity());
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setAnonymous
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getAnonymous
     */
    public function testGetAndSetAnonymous() {
        try {
            $this->assertSame(false, $this->object->getAnonymous());
            $this->object->setAnonymous(true);
            $this->assertSame(true, $this->object->getAnonymous());

            // Tests PHP 7 : typecasting bool
            $this->object->setAnonymous(0); // (bool)false
            $this->object->setAnonymous(1.5); // (bool)true
            $this->object->setAnonymous("1"); // (bool)true
            $this->object->setAnonymous(""); // (bool)false
            $this->object->setAnonymous("azerty"); // (bool)true
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setAnonymous
     *
     * @expectedException \TypeError
     */
    public function testSetAnonymousWithNullParam() {
        $this->object->setAnonymous(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setStatus
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getStatus
     */
    public function testGetAndSetStatus() {
        try {
            $this->assertSame(0, $this->object->getStatus());
            $this->object->setStatus(1);
            $this->assertSame(1, $this->object->getStatus());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setStatus
     *
     * @expectedException \TypeError
     */
    public function testSetStatusWithNullParam() {
        $this->object->setStatus(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setIp
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getIp
     */
    public function testGetAndSetIp() {
        try {
            $this->object->setIp("127.0.0.1");
            $this->assertSame("127.0.0.1", $this->object->getIp());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setIp
     *
     * @expectedException \TypeError
     */
    public function testSetIpWithNullParam() {
        $this->object->setIp(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::setAuthor
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getAuthor
     */
    public function testGetAndSetAuthor() {
        try {
            $user = new User();
            $this->object->setAuthor($user);
            $this->assertSame($user, $this->object->getAuthor());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::addResumeSkill
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getResumeSkills
     */
    public function testAddResumeSkill() {
        try {
            $this->assertInstanceOf(Collection::class, $this->object->getResumeSkills());
            $this->assertCount(0, $this->object->getResumeSkills());

            $resumeSkill = new ResumeSkill();
            $this->object->addResumeSkill($resumeSkill);
            $this->assertSame($resumeSkill, $this->object->getResumeSkills()->get(0));
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::removeResumeSkill
     */
    public function testRemoveResumeSkill() {
        try {
            $resumeSkill = new ResumeSkill();
            $this->object->addResumeSkill($resumeSkill);
            $this->assertCount(1, $this->object->getResumeSkills());
            $this->object->removeResumeSkill($resumeSkill);
            $this->assertCount(0, $this->object->getResumeSkills());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::addExperience
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getExperiences
     */
    public function testAddExperience() {
        try {
            $this->assertInstanceOf(Collection::class, $this->object->getExperiences());
            $this->assertCount(0, $this->object->getExperiences());

            $experience = new Experience();
            $this->object->addExperience($experience);
            $this->assertSame($experience, $this->object->getExperiences()->get(0));
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::removeExperience
     */
    public function testRemoveExperience() {
        try {
            $experience = new Experience();
            $this->object->addExperience($experience);
            $this->assertCount(1, $this->object->getExperiences());
            $this->object->removeExperience($experience);
            $this->assertCount(0, $this->object->getExperiences());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::addEducation
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getEducations
     */
    public function testAddEducation() {
        try {
            $this->assertInstanceOf(Collection::class, $this->object->getEducations());
            $this->assertCount(0, $this->object->getEducations());

            $education = new Education();
            $this->object->addEducation($education);
            $this->assertSame($education, $this->object->getEducations()->get(0));
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::removeEducation
     */
    public function testRemoveEducation() {
        try {
            $education = new Education();
            $this->object->addEducation($education);
            $this->assertCount(1, $this->object->getEducations());
            $this->object->removeEducation($education);
            $this->assertCount(0, $this->object->getEducations());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * Test the `getTranslationEntityClass` static method.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Resume::getTranslationEntityClass
     *
     * @return void
     */
    public function testGetTranslationEntityClass(): void {
        $this->assertSame(Translation\ResumeTranslation::class, Resume::getTranslationEntityClass(), "1. The translation class expected is not ok.");
    }

    /**
     * Test the validation of the entity and its translations.
     *
     * @return void
     */
    public function testValidationWithTranslations(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $resume = new Resume();
        $resume->setCurrentLocale("fr")
                ->setTitle("");
        $resume->translate("en")->setTitle("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam luctus tincidunt elit vel cras amet bad.");

        $errors = $validator->validate($resume);
        $this->assertCount(2, $errors, "1. Two errors are expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];

        $this->assertInstanceOf(NotBlank::class, $error1->getConstraint(), "2.1 The error has to be a NotNull constraint.");
        $this->assertInstanceOf(Length::class, $error2->getConstraint(), "2.2. The error has to be a Length constraint.");
    }

    /**
     * Test the setSlug proxy method.
     *
     * @return void
     */
    public function testSetSlug(): void {
        $resumeTranslation = $this->object->setCurrentLocale("fr")
                ->setSlug("resume-slug");

        $this->assertEquals("resume-slug", $this->object->getSlug(), "1. The slug has to be well set.");
        $this->assertInstanceOf(Translation\ResumeTranslation::class, $resumeTranslation, "2. The proxy method has to return a [ResumeTranslation] object.");
        $this->assertEquals("resume-slug", $resumeTranslation->getSlug(), "3. The slug property has to be well set.");
    }

    /**
     * Test the `setSlug` proxy method.
     *
     * @return void
     */
    public function testSetSlugWithBadValue(): void {
        $badValues = [
            "1" => null,
            "2" => new \stdClass(),
            "3" => []
        ];

        $this->object->setCurrentLocale("fr");
        foreach ($badValues as $key => $value) {
            try {
                $this->object->setSlug($value);
                $this->fail("A [TypeError] error is expected for value: $value.");
            } catch (\Throwable $exc) {
                $this->assertInstanceOf(\TypeError::class, $exc,
                        $key . ". The exception expected is not ok: " . $exc->getMessage()
                );
            }
        }
    }

}
