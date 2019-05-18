<?php

namespace Com\Nairus\CoreBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Entity\ContactMessage;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Load ContactMessage test entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadContactMessage implements FixtureInterface {

    /**
     * Load test entities.
     *
     * @param ObjectManager $manager The entity manager.
     */
    public function load(ObjectManager $manager) {
        $contactMessage = new ContactMessage();
        $contactMessage->setEmail("goku@dbsuper.com")
                ->setIp("127.0.0.1")
                ->setMessage("Bonjour le monde")
                ->setName("Son Goku")
                ->setRequestDate(new \DateTime());

        $manager->persist($contactMessage);
        $manager->flush();
        $manager->clear(ContactMessage::class);
    }

}
