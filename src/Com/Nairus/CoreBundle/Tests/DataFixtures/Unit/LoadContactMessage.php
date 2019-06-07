<?php

namespace Com\Nairus\CoreBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Entity\ContactMessage;
use Com\Nairus\CoreBundle\Entity\BlacklistedIp;
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
        $currentDate = new \DateTimeImmutable();
        $datas = [
            1 => $currentDate,
            2 => $currentDate->add(new \DateInterval("P1D")),
            3 => $currentDate->add(new \DateInterval("P2D"))
        ];

        foreach ($datas as $ip => $requestDate) {
            $contactMessage = new ContactMessage();
            $contactMessage->setEmail("goku@dbsuper.com")
                    ->setIp("127.0.0.$ip")
                    ->setMessage("Bonjour le monde")
                    ->setName("Son Goku")
                    ->setRequestDate($requestDate);
            $manager->persist($contactMessage);

            // If this is an odd digit, we blacklist the ip
            if ($ip % 2) {
                $blacklistedIp = new BlacklistedIp();
                $blacklistedIp->setBlacklistedAt(new \DateTime())
                        ->setIp($contactMessage->getIp());
                $manager->persist($blacklistedIp);
            }
        }

        $manager->flush();
        $manager->clear();
    }

}
