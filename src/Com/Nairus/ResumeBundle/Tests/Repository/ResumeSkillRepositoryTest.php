<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeSkill;
use Com\Nairus\UserBundle\NSUserBundle;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * ResumeSkillRepository class test.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ResumeSkillRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ResumeSkillRepository
     */
    private static $repository;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":ResumeSkill");

        // Load test fixtures.
        static::loadBeforeClass(static::$em, [new LoadSkill(), new LoadSkillLevel()]);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass() {
        // Remove test fixtures.
        static::cleanDatasAfterTest(static::$container, [new LoadSkill(), new LoadSkillLevel(), ResumeSkill::class]);
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
        /* @var $skill Skill */
        $skill = static::$em->getRepository(NSResumeBundle::NAME . ":Skill")->findOneBy(["title" => "PHP 7"]);
        /* @var $skillLevel SkillLevel */
        $skillLevel = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];

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
        $otherSkill = static::$em->getRepository(NSResumeBundle::NAME . ":Skill")->findOneBy(["title" => "Python 2/3"]);
        /* @var $skillLevel SkillLevel */
        $otherSkillLevel = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[1];
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
            $skill = static::$em->getRepository(NSResumeBundle::NAME . ":Skill")->findOneBy(["title" => "PHP 7"]);
            $skillLevel = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];

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
            $skillLevel = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];

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
            $skill = static::$em->getRepository(NSResumeBundle::NAME . ":Skill")->findOneBy(["title" => "Python 2/3"]);

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

    /**
     * Test the <code>getOrderByRank</code> method.
     *
     * @return void
     */
    public function testGetOrderByRank(): void {
        // prepare datas to test.
        $loadResumeSkill = new LoadResumeSkill();
        $loadResumeSkill->load(static::$em);

        // get the resume with resumeSkills added.
        $author = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');
        /* @var $resume Resume */
        $resume = static::$em->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // launch the test in fr.
        $resumeSkillsFr = static::$repository->findOrderedByRank($resume->getId(), "fr");
        $allResumeSkills = static::$repository->findByResume($resume);

        $this->assertCount(2, $resumeSkillsFr, "1.1 Two entities are expected.");
        $this->assertEquals(1, $resumeSkillsFr->first()->getRank(), "1.2 The first element has not the good rank.");
        $this->assertNotEquals($allResumeSkills[0]->getId(), $resumeSkillsFr->first()->getId(), "1.3 The id has to be different");
        $skillLevelTranslations = $resumeSkillsFr->first()->getSkillLevel()->getTranslations();
        $this->assertCount(1, $skillLevelTranslations, "1.4 One skill level translation is expected.");
        $this->assertArrayHasKey("fr", $skillLevelTranslations, "1.5 The translation expected has to be in fr.");

        $resumeSkillsEn = static::$repository->findOrderedByRank($resume->getId(), "en");
        $this->assertCount(0, $resumeSkillsEn, "2. No entity is expected in en.");
    }

}
