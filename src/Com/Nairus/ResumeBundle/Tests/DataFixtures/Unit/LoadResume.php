<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Com\Nairus\CoreBundle\Tests\DataFixtures\RemovableFixturesInterface;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\UserBundle\Entity\User;

/**
 * Loader for Resume entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadResume implements FixtureInterface, RemovableFixturesInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $user = $manager->getRepository(User::class)->findOneByUsername("user");

        $resume = new Resume();
        $resume->setAnonymous(true)
                ->setIp("127.0.0.1")
                ->setStatus(ResumeStatusEnum::OFFLINE_INCOMPLETE)
                ->setAuthor($user);

        $manager->persist($resume);
        $manager->flush();
        $manager->clear(Resume::class);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityManagerInterface $manager) {
        $author = $manager->getRepository(User::class)->findOneBy(["username" => "user"]);
        $resumes = $manager->getRepository(Resume::class)->findBy(["author" => $author]);

        foreach ($resumes as /* @var $resume NSResumeEntity\Resume */ $resume) {
            // Clean the datas properly .
            foreach ($resume->getEducations() as $education) {
                $resume->removeEducation($education);
                $manager->remove($education);
            }
            foreach ($resume->getExperiences() as $experience) {
                $resume->removeExperience($experience);
                $manager->remove($experience);
            }
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $resume->removeResumeSkill($resumeSkill);
                $manager->remove($resumeSkill);
            }
            $manager->remove($resume);
        }
        $manager->flush();
        $manager->clear();
    }

}
