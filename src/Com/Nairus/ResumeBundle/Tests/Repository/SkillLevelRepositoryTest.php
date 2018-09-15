<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;

/**
 * Test de la classe SkillLevelRepository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var SkillLevelRepository
     */
    private static $repository;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        // Test d'ajout d'une entité.
        $newSkillLevel = new SkillLevel();
        $newSkillLevel
                ->setCurrentLocale("fr")
                ->setTitle("Débutant");
        static::$em->persist($newSkillLevel);
        static::$em->flush();
        static::$em->clear();

        // Récupération de l'entité en base.
        $skillLevels = static::$repository->findAll();

        /* @var $entity SkillLevel */
        $entity = $skillLevels[0];
        $entity->setCurrentLocale("fr");

        $this->assertCount(1, $skillLevels, "1.1. 4 entities has to remain in database.");
        $this->assertSame($newSkillLevel->getId(), $entity->getId(), "1.2. The id has to be identical.");
        $this->assertTrue($entity->hasTranslation("fr"), "1.3. The entity has to have a [fr] translation for [title] field.");
        $this->assertSame("Débutant", $entity->getTitle(), "1.2. The default title translation has to be identical.");

        // Update test.
        $entity->setTitle("Expert");
        static::$em->flush();
        static::$em->clear();

        /* @var $skillLevel SkillLevel */
        $skillLevel = static::$repository->find($entity->getId());
        $this->assertSame("Expert", $skillLevel->getTitle(), "2.1. The title has to be identical.");

        // Test add new translation.
        $skillLevel->translate("en")->setTitle("The best");
        static::$em->flush($skillLevel);
        static::$em->refresh($skillLevel);

        $this->assertTrue($skillLevel->hasTranslation("en"), "3.1. The entity has to have a [fr] translation for [title] field.");
        $this->assertSame("The best", $skillLevel->setCurrentLocale("en")->getTitle(), "3.2. The [en] title translation has to be identical.");

        // Delete test.
        $id = $skillLevel->getId();
        static::$em->remove($skillLevel);
        static::$em->flush();
        static::$em->clear();

        $skillLevelRemoved = static::$repository->find($id);
        $this->assertNull($skillLevelRemoved, "4.1. The entity has to be removed.");
    }

}
