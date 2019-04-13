<?php

namespace Com\Nairus\CoreBundle\Tests\DataFixtures;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface for removable data fixtures.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface RemovableFixturesInterface {

    /**
     * Remove all entities in the database.
     *
     * @param EntityManagerInterface $manager The entity manager instance.
     *
     * @return mixed
     */
    public function remove(EntityManagerInterface $manager);
}
