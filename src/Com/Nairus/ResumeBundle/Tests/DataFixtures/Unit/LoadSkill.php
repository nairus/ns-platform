<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Skills datas test set.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadSkill implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $skills = [
            "PHP 7",
            "Python 2/3"
        ];

        foreach ($skills as $title) {
            $skill = new Skill();
            $skill->setTitle($title);
            $manager->persist($skill);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        $className = Skill::class;
        $classMetadata = $manager->getClassMetadata($className);
        $connection = $manager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('PRAGMA foreign_keys = OFF');
            $q = $databasePlatform->getTruncateTableSql($classMetadata->getTableName());
            $result = $connection->executeUpdate($q);
            $connection->query('PRAGMA foreign_keys = ON');
            $connection->commit();
            return $result;
        } catch (\Exception $exc) {
            $connection->rollBack();
            throw $exc;
        }
    }

}
