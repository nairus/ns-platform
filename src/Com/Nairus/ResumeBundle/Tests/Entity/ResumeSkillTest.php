<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as SFConstaints;

/**
 * ResumeSkill unit tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillTest extends AbstractKernelTestCase {

    /**
     * @var ResumeSkill
     */
    protected $object;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     *
     * @var Resume
     */
    private static $resume;

    /**
     * Load fixtures for testing.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        $loadSkill = new LoadSkill();
        $loadSkill->load(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->load(static::$em);
        $loadResumeOnline = new LoadResumeOnline();
        $loadResumeOnline->load(static::$em);

        // get the first resume online
        static::$resume = static::$em->getRepository(Resume::class)->findOneBy(["status" => ResumeStatusEnum::ONLINE]);
    }

    /**
     * Remove fixtures for others tests.
     */
    public static function tearDownAfterClass() {
        static::$resume = null;
        $loadResumeOnline = new LoadResumeOnline();
        $loadResumeOnline->remove(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->remove(static::$em);
        $loadSkill = new LoadSkill();
        $loadSkill->remove(static::$em);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ResumeSkill();
        $this->validator = static::$container->get("validator");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object, $this->validator);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::setRank
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::getRank
     */
    public function testGetAndSetRank() {
        try {
            $this->object->setRank(1);
            $this->assertSame(1, $this->object->getRank());
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::setResume
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::getResume
     */
    public function testGetAndSetResume() {
        try {
            $resume = new Resume();
            $this->object->setResume($resume);
            $this->assertSame($resume, $this->object->getResume());
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::setSkill
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::getSkill
     */
    public function testGetAndSetSkill() {
        try {
            $skill = new Skill();
            $this->object->setSkill($skill);
            $this->assertSame($skill, $this->object->getSkill());
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::setSkillLevel
     * @covers Com\Nairus\ResumeBundle\Entity\ResumeSkill::getSkillLevel
     */
    public function testGetAndSetSkillLevel() {
        try {
            $skillLevel = new SkillLevel();
            $this->object->setSkillLevel($skillLevel);
            $this->assertSame($skillLevel, $this->object->getSkillLevel());
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * Test the validation of rank entity field unique.
     */
    public function testValidationRankNotUnique() {


        $this->object->setRank(1)
                ->setResume(static::$resume)
                ->setSkill(new Skill())
                ->setSkillLevel(new SkillLevel());

        $errors = $this->validator->validate($this->object);

        $this->assertCount(1, $errors, "1.1 1 error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(UniqueEntity::class, $error1->getConstraint(), "1.2 The error has to be a UniqueEntity constraint.");
    }

    /**
     * Test the validation of entity fields null.
     */
    public function testValidationNotNull() {
        $this->object->setRank(2);
        $errors = $this->validator->validate($this->object);

        $this->assertCount(3, $errors, "1.1 3 errors are expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotNull::class, $error1->getConstraint(), "1.2 The error has to be a NotNull constraint.");
        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];
        $this->assertInstanceOf(SFConstaints\NotNull::class, $error2->getConstraint(), "1.3 The error has to be a NotNull constraint.");
        /* @var $error3 \Symfony\Component\Validator\ConstraintViolation */
        $error3 = $errors[2];
        $this->assertInstanceOf(SFConstaints\NotNull::class, $error3->getConstraint(), "1.4 The error has to be a NotNull constraint.");
    }

    /**
     * Test the validation of rank entity field greater than 0.
     */
    public function testValidationRankGreaterThan() {
        $this->object->setRank(0)
                ->setResume(new Resume())
                ->setSkill(new Skill())
                ->setSkillLevel(new SkillLevel);

        $errors = $this->validator->validate($this->object);

        $this->assertCount(1, $errors, "1.1 1 error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\GreaterThan::class, $error1->getConstraint(), "1.2 The error has to be a GreaterThan constraint.");
    }

}
