<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\ResumeBundle\Entity\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Jeu de test des compétences.
 *
 * @author nairus
 */
class LoadSkill extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $skills = [
            "PHP 7",
            "Python 2/3"
        ];

        foreach ($skills as $title) {
            $skill = new Skill();
            $skill->setTitle($title);
            $manager->persist($skill);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 4;
    }

}
