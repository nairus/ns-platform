<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\ResumeBundle\Enums\UserRolesEnum;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Jeu de test des cv.
 *
 * @author nairus
 */
class LoadResume extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $author = $this->getReference("user_" . UserRolesEnum::AUTHOR);
        $resume = new Resume();
        $resume
                ->setTitle("Default title")
                ->setIp("127.0.0.1")
                ->setAuthor($author);

        $manager->persist($resume);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 3;
    }

}
