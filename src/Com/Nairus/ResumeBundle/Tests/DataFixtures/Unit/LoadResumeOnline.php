<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation;
use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\UserBundle\NSUserBundle;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Management of tests set for online resumes (one shot).
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadResumeOnline implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void {
        $user = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('moderator');
        $profile = new Profile();
        $profile->setAddress("Adresse 4")
                ->setAddressAddition("Adresse 5")
                ->setCell("06.02.02.02.02")
                ->setCity("Istres")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04.02.01.01.01")
                ->setLastName("Nom")
                ->setZip("13800")
                ->setUser($user);

        $manager->persist($profile);
        $this->prepareResumeDatas($manager, $user);
        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        /* @var $profileRepository ProfileRepository */
        $profileRepository = $manager->getRepository(NSResumeBundle::NAME . ":Profile");
        /* @var $userRepository ObjectRepository */
        $userRepository = $manager->getRepository(NSUserBundle::NAME . ":User");

        // Get the Resume to remove.
        $dql = "SELECT r FROM NSResumeBundle:Resume r WHERE r.status = :status";
        $resumes = $manager
                ->createQuery($dql)
                ->setParameter("status", ResumeStatusEnum::ONLINE)
                ->getResult();

        foreach ($resumes as /* @var $resume Resume */ $resume) {

            // Remove [Education] entities linked to the current resume.
            foreach ($resume->getEducations() as $education) {
                $resume->removeEducation($education);
                $manager->remove($education);
            }

            // Remove [Experiences] entities linked to the current resume.
            foreach ($resume->getExperiences() as $experience) {
                $resume->removeExperience($experience);
                $manager->remove($experience);
            }

            // Remove [ResumeSkill] entities linked to the current resume.
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $resume->removeResumeSkill($resumeSkill);
                $manager->remove($resumeSkill);
            }

            $manager->remove($resume);
        }

        /* @var $user User */
        $user = $userRepository->findOneByUsername('moderator');

        /* @var $profile Profile */
        $profile = $profileRepository->findOneByUser($user);
        if ($profile) {
            $manager->remove($profile);
            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * Prepare the datas for the resume.
     *
     * @param ObjectManager $manager The entity manager.
     */
    private function prepareResumeDatas(EntityManagerInterface $manager, User $user): void {
        /* @var $skill Skill */
        $skill = $manager->getRepository(NSResumeBundle::NAME . ":Skill")->findOneByTitle("PHP 7");
        /* @var $skillLevel SkillLevel */
        $skillLevel = $manager->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];

        for ($index = 0; $index < 3; $index++) {
            $creationDate = new \DateTime("7 days ago");
            $creationDate->add(new \DateInterval("P" . ($index + 1) . "D"));
            $resume = new Resume();
            $resume
                    ->setCurrentLocale("fr")
                    ->setIp("127.0.0.1")
                    ->setAuthor($user)
                    ->setStatus(ResumeStatusEnum::ONLINE)
                    ->setCreatedAt($creationDate)
                    ->setTitle("Test$index fr");

            // Adding en translation for even index
            if (0 === $index % 2) {
                $resumeTranslation = new ResumeTranslation();
                $resumeTranslation->setLocale("en")
                        ->setTitle("Test$index en");
                $resume->addTranslation($resumeTranslation);
            }

            $manager->persist($resume);

            // On insère des données nécessaires pour les 2 premiers cv uniquement.
            if ($index < 2) {
                $this->buildDependencies($manager, $resume, $skill, $skillLevel);
            }
        }
    }

    /**
     * Build the dependencies of the resume.
     *
     * @param EntityManagerInterface $manager    The entity manager.
     * @param Resume                 $resume     The current resume.
     * @param Skill                  $skill      The skill entity linked.
     * @param SkillLevel             $skillLevel The skill level entity.
     *
     * @return void
     */
    private function buildDependencies(EntityManagerInterface $manager, Resume $resume, Skill $skill, SkillLevel $skillLevel): void {
        $education = new Education();
        $education
                ->setCurrentLocale("fr")
                ->setDiploma("BTS")
                ->setDomain("Informatique")
                ->setEndYear(2006)
                ->setInstitution("AFPA")
                ->setStartYear(2005)
                ->setResume($resume)
                ->setDescription("Description")
        ;

        $experience = new Experience();
        $experience
                ->setCompany("Société")
                ->setCurrentLocale("fr")
                ->setEndMonth(12)
                ->setEndYear(2017)
                ->setLocation("Marseille")
                ->setStartMonth(1)
                ->setStartYear(2017)
                ->setResume($resume)
                ->setDescription("Description");

        $resumeSkill = new ResumeSkill();
        $resumeSkill
                ->setRank(1)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel)
                ->setResume($resume);

        $manager->persist($resumeSkill);
        $manager->persist($education);
        $manager->persist($experience);
    }

}
