<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Tests\AbstractKernelTestCase;

/**
 * Test de la classe SkillLevelRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class SkillLevelRepositoryTest extends AbstractKernelTestCase
{

    /**
     * @var SkillLevelRepository
     */
    private static $repository;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel");
    }

    /**
     * Test d'insertion, de mise à jour et de suppression de l'entité.
     */
    public function testInsertUpdateAndDelete()
    {
        // Test d'ajout d'une entité.
        $newSkillLevel = new SkillLevel();
        $newSkillLevel->setTitle("Débutant");
        static::$em->persist($newSkillLevel);
        static::$em->flush();
        static::$em->clear();

        // Récupération de l'entité en base.
        $skillLevels = static::$repository->findAll();
        $this->assertCount(4, $skillLevels, "1.1. Il y avoir 4 entités en base.");
        $this->assertSame($newSkillLevel->getId(), $skillLevels[3]->getId(), "1.2. L'id récupérée doit être identique à celle créée.");

        // Test de mise à jour.
        /* @var $entity SkillLevel */
        $entity = $skillLevels[3];
        $entity->setTitle("Expert");
        static::$em->flush();
        static::$em->clear();

        /* @var $skillLevel SkillLevel */
        $skillLevel = static::$repository->find($entity->getId());
        $this->assertSame("Expert", $skillLevel->getTitle(), "2.1. Le titre de l'entité récupérée doit être mise à jour.");

        // Test de suppression.
        $id = $skillLevel->getId();
        static::$em->remove($skillLevel);
        static::$em->flush();
        static::$em->clear();

        $skillLevelRemoved = static::$repository->find($id);
        $this->assertNull($skillLevelRemoved, "3.1. L'entité doit être supprimée.");
    }

}
