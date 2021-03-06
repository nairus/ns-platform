<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Resumes datas test set.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadResume extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $author = $this->getReference("user_" . UserRolesEnum::AUTHOR);
        $resume = new Resume();
        $resume
                ->setIp("127.0.0.1")
                ->setAuthor($author)
                ->setCurrentLocale("fr")
                ->setTitle("Titre par défaut");

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
