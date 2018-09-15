<?php

namespace Com\Nairus\CoreBundle\Tests\Traits;

/**
 * Trait for DatasCleaner behaviors.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait DatasCleanerTrait {

    /**
     * Clean datas for next tests.
     *
     * @return void
     */
    protected function cleanDatas(array $entityClasses): void {
        // Reset the entity manager to prevent "Doctrine\ORM\ORMException".
        static::$kernel->getContainer()
                ->get("doctrine")
                ->resetManager();

        $entityManager = static::$kernel->getContainer()
                ->get("doctrine")
                ->getManager();

        $connection = $entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('PRAGMA foreign_keys = OFF');
            foreach ($entityClasses as $className) {
                $entityMetadata = $entityManager->getClassMetadata($className);
                $query = $databasePlatform->getTruncateTableSql($entityMetadata->getTableName());
                $connection->executeUpdate($query);
            }
            $connection->query('PRAGMA foreign_keys = ON');
            $connection->commit();
        } catch (\Exception $exc) {
            $connection->rollBack();
            throw $exc;
        }
    }

}
