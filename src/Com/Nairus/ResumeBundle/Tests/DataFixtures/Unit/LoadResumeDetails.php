<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\ResumeBundle\Entity as NSResumeEntity;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Load resume details for online page.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadResumeDetails implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $user = $manager->getRepository(User::class)->findOneByUsername(\Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase::ADMIN);

        $DS = DIRECTORY_SEPARATOR;
        $path = __DIR__ . $DS . "../../../../../../../tests" . $DS . "resources" . $DS . "image-to-resize.png";
        $avatar = new NSResumeEntity\Avatar();

        $avatar->setImageFile(new UploadedFile(realpath($path), "image-to-resize.png"));
        $profile = new NSResumeEntity\Profile();
        $profile->setAddress("Adresse 4")
                ->setAddressAddition("Adresse 5")
                ->setCell("06.02.02.02.02")
                ->setCity("Istres")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04.02.01.01.01")
                ->setLastName("Nom")
                ->setZip("13800")
                ->setUser($user)
                ->setAvatar($avatar);

        $manager->persist($profile);
        $this->prepareResumeDatas($manager, $user);
        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        $user = $manager->getRepository(User::class)->findOneByUsername(\Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase::ADMIN);
        $profile = $manager->getRepository(NSResumeEntity\Profile::class)->findOneByUser($user);
        $resumes = $manager->getRepository(NSResumeEntity\Resume::class)->findByAuthor($user);

        if (null !== $profile) {
            $manager->remove($profile);
        }

        foreach ($resumes as /* @var $resume NSResumeEntity\Resume */ $resume) {
            foreach ($resume->getEducations() as $education) {
                $manager->remove($education);
                $resume->removeEducation($education);
            }

            foreach ($resume->getExperiences() as $experience) {
                $manager->remove($experience);
                $resume->removeExperience($experience);
            }

            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $manager->remove($resumeSkill);
                $resume->removeResumeSkill($resumeSkill);
            }

            $manager->remove($resume);
        }

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
        $skills = $manager->getRepository(NSResumeEntity\Skill::class)->findAll();

        // create 3 resumes:
        // - the first and the third are anonymous
        // - the third is offline
        for ($i = 0; $i < 3; $i++) {
            $num = $i + 1;
            $resume = new NSResumeEntity\Resume();
            $resume->setAnonymous($i % 2)
                    ->setAuthor($user)
                    ->setIp("127.0.0.1")
                    ->setStatus($num % 3 ? ResumeStatusEnum::ONLINE : ResumeStatusEnum::OFFLINE_INCOMPLETE)
                    ->setCurrentLocale("fr")
                    ->setTitle("Title FR $num");
            $resumeTranslation = $resume->translate("en");
            $resumeTranslation->setTitle("Title EN $num");

            $education = new NSResumeEntity\Education();
            $education
                    ->setCurrentLocale("fr")
                    ->setDiploma("BTS")
                    ->setDomain("Informatique")
                    ->setEndYear(2006)
                    ->setInstitution("AFPA")
                    ->setStartYear(2005)
                    ->setResume($resume)
                    ->setDescription("Description $num")
            ;

            $experience = new NSResumeEntity\Experience();
            $experience
                    ->setCompany("Société")
                    ->setCurrentLocale("fr")
                    ->setEndMonth(12)
                    ->setEndYear(2017)
                    ->setLocation("Marseille")
                    ->setStartMonth(1)
                    ->setStartYear(2017)
                    ->setResume($resume)
                    ->setDescription("Description $num");

            $skillLevel = new NSResumeEntity\SkillLevel();
            $skillLevel->setCurrentLocale("fr")
                    ->setTitle("Skill level FR $num");

            $resumeSkill = new NSResumeEntity\ResumeSkill();
            $resumeSkill
                    ->setRank(1)
                    ->setSkill($skills[$num % 2])
                    ->setSkillLevel($skillLevel)
                    ->setResume($resume);

            // add en translations for the first and the third entity.
            if ($i % 2 === 0) {
                /* @var $educationEn NSResumeEntity\Translation\EducationTranslation */
                $educationEn = $education->translate("en");
                $educationEn->setDescription("Desc EN $num")
                        ->setDomain("Domain");

                /* @var $experienceEn NSResumeEntity\Translation\ExperienceTranslation */
                $experienceEn = $experience->translate("en");
                $experienceEn->setDescription("Desc EN $num");

                /* @var $skillLevelEn NSResumeEntity\Translation\SkillLevelTranslation */
                $skillLevelEn = $skillLevel->translate("en");
                $skillLevelEn->setTitle("Skill level EN $num");
            }

            $manager->persist($skillLevel);
            $manager->persist($resumeSkill);
            $manager->persist($education);
            $manager->persist($experience);
            $manager->persist($resume);
        }
    }

}
