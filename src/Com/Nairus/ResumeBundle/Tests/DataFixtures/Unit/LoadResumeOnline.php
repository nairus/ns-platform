<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Management of tests set for online resumes (one shot).
 *
 * @author nairus
 */
class LoadResumeOnline implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void {
        $user = new User();
        $user
                ->setUsername("me")
                ->setEmail("test@nairus.fr")
                ->setPassword("testpass")
                ->setEmailCanonical("test@nairus.fr")
                ->setEnabled(true)
                ->addRole(UserRolesEnum::AUTHOR);

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

        $manager->persist($user);
        $manager->persist($profile);
        $this->prepareResumeDatas($manager, $user);
        $manager->flush();
        $manager->clear();
    }

    /**
     * Remove the tests set.
     *
     * @param ObjectManager $manager The entity manager instance.
     *
     * @return void
     */
    public function remove(EntityManagerInterface $manager): void {
        /* @var $educationRepository EntityRepository */
        $educationRepository = $manager->getRepository(NSResumeBundle::NAME . ":Education");
        /* @var $experienceRepository EntityRepository */
        $experienceRepository = $manager->getRepository(NSResumeBundle::NAME . ":Experience");
        /* @var $profileRepository ProfileRepository */
        $profileRepository = $manager->getRepository(NSResumeBundle::NAME . ":Profile");
        /* @var $resumeSkillRepository EntityRepository */
        $resumeSkillRepository = $manager->getRepository(NSResumeBundle::NAME . ":ResumeSkill");
        /* @var $userRepository ObjectRepository */
        $userRepository = $manager->getRepository(NSUserBundle::NAME . ":User");

        // Récupération des Resume à supprimer.
        $dql = "SELECT r FROM NSResumeBundle:Resume r WHERE r.status = :status";
        $resumes = $manager
                ->createQuery($dql)
                ->setParameter("status", ResumeStatusEnum::ONLINE)
                ->getResult();

        foreach ($resumes as $resume) {
            /* @var $resume Resume */
            $resume;

            // Suppression des entités [Education].
            $educations = $educationRepository->findByResume($resume);
            foreach ($educations as $education) {
                $manager->remove($education);
            }

            $experiences = $experienceRepository->findByResume($resume);
            foreach ($experiences as $experience) {
                $manager->remove($experience);
            }

            $resumeSkills = $resumeSkillRepository->findByResume($resume);
            foreach ($resumeSkills as $resumeSkill) {
                $manager->remove($resumeSkill);
            }

            $manager->remove($resume);
        }

        /* @var $user User */
        $user = $userRepository->findOneByUsername("me");

        /* @var $profile Profile */
        $profile = $profileRepository->findOneBy(["user" => $user]);
        $manager->remove($profile);
        $manager->remove($user);
        $manager->flush();
        $manager->clear();
    }

    /**
     * Prepare the datas for the resume.
     *
     * @param ObjectManager $manager The entity manager.
     */
    private function prepareResumeDatas(EntityManagerInterface $manager, User $user): void {
        /* @var $skill Skill */
        $skill = $manager->getRepository(NSResumeBundle::NAME . ":Skill")->findOneBy(["title" => "PHP 7"]);
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
                    ->setTitle("Test" . $index);
            $manager->persist($resume);

            // On insère des données nécessaires pour les 2 premiers cv uniquement.
            if ($index < 2) {
                $this->buildDependencies($manager, $resume, $skill, $skillLevel);
            }
        }
    }

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
