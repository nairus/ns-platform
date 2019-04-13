<?php

namespace Com\Nairus\CoreBundle\Tests\Traits;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait for DatasCleaner behaviors.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait DatasCleanerTrait {

    /**
     * Clean datas for static afterClass method.
     *
     * @param ContainerInterface $container     The services container.
     * @param array              $entityClasses The entities to remove.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected static function cleanDatasAfterTest(ContainerInterface $container, array $entityClasses): void {
        // Reset the entity manager to prevent "Doctrine\ORM\ORMException".
        $container
                ->get("doctrine")
                ->resetManager();

        $entityManager = $container
                ->get("doctrine")
                ->getManager();

        $connection = $entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('PRAGMA foreign_keys = OFF');
            foreach ($entityClasses as $className) {
                if ($className instanceof RemovableFixturesInterface) {
                    $className->remove($entityManager);
                } else {
                    $entityMetadata = $entityManager->getClassMetadata($className);
                    $query = $databasePlatform->getTruncateTableSql($entityMetadata->getTableName());
                    $connection->executeUpdate($query);
                }
            }
            $connection->query('PRAGMA foreign_keys = ON');
            $connection->commit();
        } catch (\Exception $exc) {
            $connection->rollBack();
            throw $exc;
        }
    }

    /**
     * Clean datas for next tests.
     *
     * @param ContainerInterface $container     The services container.
     * @param array              $entityClasses The entities to remove.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function cleanDatas(ContainerInterface $container, array $entityClasses): void {
        static::cleanDatasAfterTest($container, $entityClasses);
    }

}
