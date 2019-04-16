<?php

namespace Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\UserBundle\NSUserBundle;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Datas loader for ResumeSkill entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadResumeSkill implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        // get the author user
        $author = $manager->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');

        // get the author resume.
        $resume = $manager->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // get the skills collection
        $skills = $manager->getRepository(NSResumeBundle::NAME . ":Skill")->findAll();

        // get the skillLevels collection
        $skillLevels = $manager->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll();

        // add 3 resume skills with reversed order
        $rank = $total = 2;
        for ($i = 0; $i < $total; $i++) {
            $resumeSkill = new ResumeSkill();
            $resumeSkill->setRank($rank)
                    ->setResume($resume)
                    ->setSkill($skills[$i])
                    ->setSkillLevel($skillLevels[$i]);
            $manager->persist($resumeSkill);
            $rank--;
        }
        $manager->flush();
        $manager->clear();
    }

}
