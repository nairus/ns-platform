<?php

namespace Com\Nairus\CoreBundle\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait to load datas in tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait DatasLoaderTrait {

    /**
     * Load datas in tests.
     *
     * @param EntityManagerInterface $entityManager The instance of entity manager.
     * @param array                  $dataLoaders   The list of loaders.
     *
     * @return void
     */
    protected function loadDatas(EntityManagerInterface $entityManager, array $dataLoaders): void {
        foreach ($dataLoaders as $loader) {
            // If the loader is an instance of [FixtureInterface].
            if ($loader instanceof \Doctrine\Common\DataFixtures\FixtureInterface) {
                $loader->load($entityManager);
            } else {
                // Otherwise this is a simple entity to load.
                $entityManager->persist($loader);
                $entityManager->flush($loader);
            }
        }
    }

}
