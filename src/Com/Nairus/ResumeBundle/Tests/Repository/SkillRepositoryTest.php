<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;

/**
 * Test de la classe SkillRepository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var SkillRepository
     */
    private static $repository;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Skill");

        // Load test fixtures.
        $loadSkill = new LoadSkill();
        $loadSkill->load(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass() {
        // Remove test fixtures.
        $loadSkill = new LoadSkill();
        $loadSkill->remove(static::$em);
    }

    /**
     * Test entities insert, update and delete.
     *
     * @return void
     */
    public function testInsertUpdateAndDelete(): void {
        // Création d'une nouvelle compétence.
        $newSkill = new Skill();
        $newSkill->setTitle("Java 8");
        static::$em->persist($newSkill);
        static::$em->flush();
        static::$em->clear();

        // Récupération des compétences en base.
        $skills = static::$repository->findAll();
        $this->assertCount(3, $skills, "1.1. The database doesn't contain 3 entities.");
        $this->assertSame($newSkill->getId(), $skills[2]->getId(), "1.2. The ids are not identicals.");

        // Update test.
        $skills[2]->setTitle("Java 11");
        static::$em->flush();
        static::$em->clear();

        /* @var $skill Skill */
        $skill = static::$repository->find($skills[2]->getId());
        $this->assertSame("Java 11", $skill->getTitle(), "2.1 The title isn't updated.");

        // Delete test.
        $id = $skill->getId();
        static::$em->remove($skill);
        static::$em->flush();
        static::$em->clear();

        $skillRemoved = static::$repository->find($id);
        $this->assertNull($skillRemoved, "3.1. The entity is not deleted.");
    }

    /**
     * Test the `findAllForPage` method.
     *
     * @covers Com\Nairus\ResumeBundle\Repository\SkillRepository::findAllForPage
     *
     * @return void
     */
    public function testFindAllForPage(): void {
        // Page 1
        $paginatorForPage1 = static::$repository->findAllForPage(0, 1);
        $this->assertSame(2, $paginatorForPage1->count(), "1.1. The paginator has not the total expected.");
        $collForPage1 = $paginatorForPage1->getIterator()->getArrayCopy();
        $this->assertCount(1, $collForPage1, "1.2. The paginator doesn't contain the expected entity.");
        /* @var $skillForPage1 Skill */
        $skillForPage1 = $collForPage1[0];
        $this->assertInstanceOf(Skill::class, $skillForPage1, "1.3. The entity class is not expected.");
        $this->assertEquals("PHP 7", $skillForPage1->getTitle(), "1.4. The entity's title is not the one expected.");

        // Page 2
        $paginatorForPage2 = static::$repository->findAllForPage(1, 1);
        $collForPage2 = $paginatorForPage2->getIterator()->getArrayCopy();
        $this->assertCount(1, $collForPage2, "2.1. The paginator doesn't contain the expected entity.");
        /* @var $skillForPage2 Skill */
        $skillForPage2 = $collForPage2[0];
        $this->assertEquals("Python 2/3", $skillForPage2->getTitle(), "2.2. The entity's title is not the one expected.");

        // Page 3
        $paginatorForPage3 = static::$repository->findAllForPage(2, 1);
        $this->assertCount(0, $paginatorForPage3->getIterator()->getArrayCopy(), "3. The paginator is not empty has expected.");
    }

}
