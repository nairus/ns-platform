<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\ResumeBundle\Entity\Avatar;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Loader for Avatar entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadProfile implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $user = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername("sadmin");
        $DS = DIRECTORY_SEPARATOR;
        $path = __DIR__ . $DS . "../../../../../../../tests" . $DS . "resources" . $DS . "image-to-resize.png";
        $avatar = new Avatar();
        $avatar->setImageFile(new UploadedFile(realpath($path), "image-to-resize.png"))
                ->setRelativePath("/web/upload/avatar/");

        $profile = new Profile();
        $profile
                ->setAddress("Adresse 1")
                ->setAddressAddition("Adresse 2")
                ->setCell("06 01 01 01 01")
                ->setCity("Marseille")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04 01 01 01 01")
                ->setLastName("Nom")
                ->setZip("13004")
                ->setUser($user)
                ->setAvatar($avatar);
        $manager->persist($profile);
        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        $user = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername("sadmin");
        /* @var $profile Profile */
        $profile = $manager->getRepository(NSResumeBundle::NAME . ":Profile")->findOneByUser($user);

        // truncate avatar table to avoid ImageEntityListener error.
        // then remove the profile.
        $className = Avatar::class;
        $classMetadata = $manager->getClassMetadata($className);
        $connection = $manager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            if ($profile) {
                $profile->setAvatar(null);
                $manager->remove($profile);
                $manager->flush();
                $manager->clear();
            }
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
