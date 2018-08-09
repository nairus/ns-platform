<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Entity\Skill;

/**
 * Test de la classe SkillRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class SkillRepositoryTest extends AbstractKernelTestCase
{

    /**
     * @var SkillRepository
     */
    private static $repository;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Skill");
    }

    /**
     * Test d'insertion, de mise à jour et de suppression de l'entité.
     *
     * @todo implement testInsertUpdateAndDelete
     */
    public function testInsertUpdateAndDelete()
    {
        // Création d'une nouvelle compétence.
        $newSkill = new Skill();
        $newSkill->setTitle("PHP 7");
        static::$em->persist($newSkill);
        static::$em->flush();
        static::$em->clear();

        // Récupération des compétences en base.
        $skills = static::$repository->findAll();
        $this->assertCount(3, $skills, "1.1. Il doit y avoir 3 entité3 en base.");
        $this->assertSame($newSkill->getId(), $skills[2]->getId(), "1.2. L'id de l'entité récupérée doit être identique à celle créée.");

        // Test de mise à jour.
        $skills[2]->setTitle("PHP 7.1");
        static::$em->flush();
        static::$em->clear();

        /* @var $skill Skill */
        $skill = static::$repository->find($skills[2]->getId());
        $this->assertSame("PHP 7.1", $skill->getTitle(), "2.1 Le titre de l'entité récupérée doit être mis à jour.");

        // Test de suppression.
        $id = $skill->getId();
        static::$em->remove($skill);
        static::$em->flush();
        static::$em->clear();

        $skillRemoved = static::$repository->find($id);
        $this->assertNull($skillRemoved, "3.1. L'entité doit être  supprimée.");
    }

}
