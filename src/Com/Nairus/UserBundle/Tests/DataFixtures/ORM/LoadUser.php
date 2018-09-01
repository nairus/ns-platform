<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\ORM;

use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Users fixtures.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadUser extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $namesList = [
            "author" => UserRolesEnum::AUTHOR,
            "moderator" => UserRolesEnum::MODERATOR,
            "sadmin" => UserRolesEnum::SUPER_ADMIN
        ];

        foreach ($namesList as $name => $role) {
            $user = new User();
            $user
                    ->setPassword($name . "pass")
                    ->addRole($role)
                    ->setUsername($name)
                    ->setEmail($name . "@test.com");
            $manager->persist($user);

            $this->addReference("user_$role", $user);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 1;
    }

}
