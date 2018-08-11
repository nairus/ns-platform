<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * Test de la classe ResumeSkillRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ResumeSkillRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ResumeSkillRepository
     */
    private static $repository;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":ResumeSkill");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
        /* @var $skill Skill */
        $skill = static::$em->find(NSResumeBundle::NAME . ":Skill", 1);
        /* @var $skillLevel SkillLevel */
        $skillLevel = static::$em->find(NSResumeBundle::NAME . ":SkillLevel", 1);

        // Test d'insertion.
        $newResumeSkill = new ResumeSkill();
        $newResumeSkill
                ->setRank(1)
                ->setResume($resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);

        static::$em->persist($newResumeSkill);
        static::$em->flush();
        static::$em->clear();

        $resumeSkills = static::$repository->findAll();
        $this->assertCount(1, $resumeSkills, "1.1. Il doit y avoir une entité en base.");
        /* @var $resumeSkill ResumeSkill */
        $resumeSkill = $resumeSkills[0];
        $this->assertSame($newResumeSkill->getId(), $resumeSkill->getId(), "1.2. L'id de l'entité créée doit être identique à celle ");
        $this->assertSame(1, $resumeSkill->getRank(), "1.3. Le rang doit être identique.");
        $this->assertSame($resume->getId(), $resumeSkill->getResume()->getId(), "1.4. Le [Resume] associé doit être identique.");
        $this->assertSame($skill->getId(), $resumeSkill->getSkill()->getId(), "1.5. Le [Skill] associé doit être identique.");
        $this->assertSame($skillLevel->getId(), $resumeSkill->getSkillLevel()->getId(), "1.6. Le [SkillLevel] associé doit être identique.");

        // Update test.
        /* @var $skill Skill */
        $otherSkill = static::$em->find(NSResumeBundle::NAME . ":Skill", 2);
        /* @var $skillLevel SkillLevel */
        $otherSkillLevel = static::$em->find(NSResumeBundle::NAME . ":SkillLevel", 2);
        $resumeSkill
                ->setRank(2)
                ->setSkill($otherSkill)
                ->setSkillLevel($otherSkillLevel);
        static::$em->flush();
        static::$em->clear();

        /* @var $resumeSkillUpdated ResumeSkill */
        $resumeSkillUpdated = static::$repository->find($resumeSkill->getId());
        $this->assertSame(2, $resumeSkillUpdated->getRank(), "1.1. Le rang doit être mis à jour.");
        $this->assertSame($otherSkill->getId(), $resumeSkillUpdated->getSkill()->getId(), "1.2. Le [Skill] associé doit être mis à jour.");
        $this->assertSame($otherSkillLevel->getId(), $resumeSkillUpdated->getSkillLevel()->getId(), "1.3. Le [SkillLevel] associé doit être mis à jour.");

        // Delete test.
        $id = $resumeSkillUpdated->getId();
        static::$em->remove($resumeSkillUpdated);
        static::$em->flush();
        static::$em->clear();

        $resumeSkillRemoved = static::$repository->find($id);
        $this->assertNull($resumeSkillRemoved, "3.1. L'entité doit être supprimée.");
    }

    /**
     * Test d'insertion d'une entité sans [Resume].
     */
    public function testInsertWithoutResume() {
        try {
            static::$em->beginTransaction();
            $skill = static::$em->find(NSResumeBundle::NAME . ":Skill", 1);
            $skillLevel = static::$em->find(NSResumeBundle::NAME . ":SkillLevel", 1);

            $newResumeSkill = new ResumeSkill();
            $newResumeSkill
                    ->setRank(1)
                    ->setSkill($skill)
                    ->setSkillLevel($skillLevel);

            static::$em->persist($newResumeSkill);
            static::$em->flush();
            $this->fail("Une exception doit être levée.");
        } catch (NotNullConstraintViolationException $exc) {
            $this->assertTrue(true);
        } catch (\Exception $exc) {
            $this->fail("Exception attendue: " . $exc->getMessage());
        } finally {
            static::$em->rollback();
            // Reset de l'EntityManager pour les tests d'exceptions suivants.
            static::$container
                    ->get("doctrine")
                    ->resetManager();
            static::$em = static::$container
                    ->get("doctrine")
                    ->getManager();
        }
    }

    /**
     * Test d'insertion d'une entité sans [Skill].
     */
    public function testInsertWithoutSkill() {
        try {
            static::$em->beginTransaction();
            $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
            $skillLevel = static::$em->find(NSResumeBundle::NAME . ":SkillLevel", 2);

            $newResumeSkill = new ResumeSkill();
            $newResumeSkill
                    ->setRank(2)
                    ->setResume($resume)
                    ->setSkillLevel($skillLevel);

            static::$em->persist($newResumeSkill);
            static::$em->flush();
            $this->fail("Une exception doit être levée.");
        } catch (NotNullConstraintViolationException $exc) {
            $this->assertTrue(true);
        } catch (\Exception $exc) {
            $this->fail("Exception attendue: " . $exc->getMessage());
        } finally {
            static::$em->rollback();
            // Reset de l'EntityManager pour les tests d'exceptions suivants.
            static::$container
                    ->get("doctrine")
                    ->resetManager();
            static::$em = static::$container
                    ->get("doctrine")
                    ->getManager();
        }
    }

    /**
     * Test d'insertion d'une entité sans [SkillLevel].
     */
    public function testInsertWithoutSkillLevel() {
        try {
            static::$em->beginTransaction();
            $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
            $skill = static::$em->find(NSResumeBundle::NAME . ":Skill", 2);

            $newResumeSkill = new ResumeSkill();
            $newResumeSkill
                    ->setRank(3)
                    ->setResume($resume)
                    ->setSkill($skill);

            static::$em->persist($newResumeSkill);
            static::$em->flush();
            $this->fail("Une exception doit être levée.");
        } catch (NotNullConstraintViolationException $exc) {
            $this->assertTrue(true);
        } catch (\Exception $exc) {
            $this->fail("Exception levée: " . $exc->getMessage());
        } finally {
            static::$em->rollback();
        }
    }

}
