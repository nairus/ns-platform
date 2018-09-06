<?php

namespace Com\Nairus\UserBundle\Tests\DataFixtures\ORM;

use Com\Nairus\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

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
        // See the security file for cost value.
        $encoder = new BCryptPasswordEncoder(12);
        foreach (AbstractUserWebTestCase::$users as $name => $role) {
            $user = new User();
            $user
                    ->setPassword($encoder->encodePassword($name . "pass", null))
                    ->addRole($role)
                    ->setUsername($name)
                    ->setEmail($name . "@test.com")
                    ->setEnabled(true);
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
