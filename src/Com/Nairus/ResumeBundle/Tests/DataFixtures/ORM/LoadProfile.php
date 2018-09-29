<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Profiles datas test set.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadProfile extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $author = $this->getReference("user_" . UserRolesEnum::AUTHOR);
        $profile = new Profile();
        $profile
                ->setAddress("Adresse 1")
                ->setAddressAddition("Adresse 2")
                ->setCell("06.01.01.01.01")
                ->setCity("Marseille")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04.01.01.01.01")
                ->setLastName("Nom")
                ->setZip("13004")
                ->setUser($author);

        $manager->persist($profile);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 2;
    }

}
