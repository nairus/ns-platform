<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Jeu de test des niveaux de compétence.
 *
 * @author nairus
 */
class LoadSkillLevel extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $skillLevels = [
            "Débutant",
            "Confirmé",
            "Expert"
        ];

        foreach ($skillLevels as $title) {
            $skillLevel = new SkillLevel();
            $skillLevel->setTitle($title);
            $manager->persist($skillLevel);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 5;
    }

}
