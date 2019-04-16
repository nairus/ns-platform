<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Datas loader for Experience entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadExperience implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        // get the author user
        $author = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');

        // get the author resume.
        $resume = $manager->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // calculate the start year
        $startYear = \date("Y") - 3;

        // insert 3 experiences every 6 monthes.
        for ($i = 0; $i < 3; $i++) {
            $experience = new Experience();
            $experience->setCompany("Company$i")
                    ->setCurrentLocale("fr")
                    ->setDescription("Description$i fr")
                    ->setLocation("Location$i")
                    ->setResume($resume);

            // add en translation for even index.
            if ($i % 2 == 0) {
                $experience->setStartMonth(1)
                        ->setStartYear($startYear)
                        ->setEndMonth(6)
                        ->setEndYear($startYear);

                /* @var $experienceTranslation ExperienceTranslation */
                $experienceTranslation = $experience->translate("en");
                $experienceTranslation->setDescription("Description$i en");
            } else {
                $experience->setStartMonth(7)
                        ->setStartYear($startYear)
                        ->setEndMonth(12)
                        ->setEndYear($startYear);

                // increment for next year.
                $startYear++;
            }

            $manager->persist($experience);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        // get the author user
        $author = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');

        // get the author resume.
        $resume = $manager->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // get all experiences to remove
        $experiences = $manager->getRepository(NSResumeBundle::NAME . ":Experience")->findByResume($resume);
        foreach ($experiences as $experience) {
            $manager->remove($experience);
        }

        $manager->flush();
        $manager->clear();
    }

}
