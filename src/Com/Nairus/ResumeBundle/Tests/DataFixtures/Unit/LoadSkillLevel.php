<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Jeu de test des niveaux de compétence.
 *
 * @author nairus
 */
class LoadSkillLevel implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $skillLevels = [
            "Débutant" => false,
            "Confirmé" => false,
            "Expert" => 'Expert'
        ];

        foreach ($skillLevels as $title => $titleEn) {
            $skillLevel = new SkillLevel();
            $skillLevel
                    ->setCurrentLocale("fr")
                    ->setTitle($title);

            if ($titleEn) {
                $skillLevel->translate('en')->setTitle($titleEn);
            }
            $manager->persist($skillLevel);
        }

        $manager->flush();
    }

    /**
     * Remove all entities in the database.
     *
     * @param EntityManagerInterface $manager The entity manager instance.
     *
     * @return mixed
     */
    public function remove(EntityManagerInterface $manager) {
        /* @var $repository ObjectRepository */
        $repository = $manager->getRepository(NSResumeBundle::NAME . ':SkillLevel');

        // We get all entities like this to remove translations onDeleteCascade.
        $entities = $repository->findAll();
        foreach ($entities as $entity) {
            $manager->remove($entity);
        }
        $manager->flush();
    }

}
