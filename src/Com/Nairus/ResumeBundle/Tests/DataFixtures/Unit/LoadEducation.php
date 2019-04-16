<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Datas loader for Education entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadEducation implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        // get the author user
        $author = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');

        // get the author resume.
        $resume = $manager->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // calculate the start year
        $startYear = \date("Y") - 4;

        // insert test datas.
        for ($i = 0; $i < 3; $i++) {
            $education = new Education();
            $education->setCurrentLocale("fr")
                    ->setDescription("Description$i fr")
                    ->setDiploma("Diplôme$i fr")
                    ->setDomain("Domaine$i fr")
                    ->setEndYear($startYear + 1)
                    ->setInstitution("Institution$i fr")
                    ->setResume($resume)
                    ->setStartYear($startYear);

            // add en translation for event index.
            if ($i % 2 == 0) {
                /* @var $translation EducationTranslation */
                $translation = $education->translate("en");
                $translation->setDescription("Description$i en")
                        ->setDomain("Domain$i en");
            }

            $manager->persist($education);

            // increment the start year.
            $startYear ++;
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

        // get all educations linked to the resume.
        $educations = $manager->getRepository(NSResumeBundle::NAME . ":Education")->findByResume($resume);

        // remove all entities with their translations (on delete cascade).
        foreach ($educations as $education) {
            $manager->remove($education);
        }

        $manager->flush();
        $manager->clear();
    }

}
