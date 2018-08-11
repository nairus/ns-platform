<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Experience;

/**
 * Test Experience Repository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ExperienceRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ExperienceRepository
     */
    private static $repository;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Experience");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);

        $newExperience = new Experience();
        $newExperience->setCompany("Société")
                ->setDescription("Description")
                ->setEndMonth(12)
                ->setEndYear(2017)
                ->setLocation("Marseille")
                ->setStartMonth(1)
                ->setStartYear(2017)
                ->setResume($resume);
        static::$em->persist($newExperience);
        static::$em->flush();
        static::$em->clear();

        $experiences = static::$repository->findAll();
        $this->assertCount(1, $experiences, "1.1. Il doit y avoir une entité en base.");
        /* @var $experience Experience */
        $experience = $experiences[0];
        $this->assertSame($newExperience->getCompany(), $experience->getCompany(), "1.2. La société doit être identique.");
        $this->assertSame($newExperience->getCurrentJob(), $experience->getCurrentJob(), "1.3. Le champ [currentJob] doit être identique.");
        $this->assertSame($newExperience->getDescription(), $experience->getDescription(), "1.4. Le champ [description] doit être identique.");
        $this->assertSame($newExperience->getEndMonth(), $experience->getEndMonth(), "1.5. Le champ [endMonth] doit être identique.");
        $this->assertSame($newExperience->getEndYear(), $experience->getEndYear(), "1.6. Le champ [endYear] doit être identique.");
        $this->assertSame($newExperience->getLocation(), $experience->getLocation(), "1.7. Le champ [location] doit être identique.");
        $this->assertSame($newExperience->getResume()->getId(), $experience->getResume()->getId(), "1.8. Le champ [resume] doit être identique.");
        $this->assertSame($newExperience->getStartMonth(), $experience->getStartMonth(), "1.9. Le champ [startMonth] doit être identique.");
        $this->assertSame($newExperience->getStartYear(), $experience->getStartYear(), "1.10. Le champ [startYear] doit être identique.");

        // Update test.
        $experience->setCompany("Société 2")
                ->setDescription("Description 2")
                ->setEndMonth(11)
                ->setEndYear(2016)
                ->setLocation("Aix")
                ->setStartMonth(2)
                ->setStartYear(2016)
                ->setCurrentJob(true);
        static::$em->flush();
        static::$em->clear();
        $experienceUpdated = static::$repository->find($experience->getId());
        $this->assertSame(2016, $experienceUpdated->getStartYear(), "1.1. Le champ [startYear] doit être identique.");
        $this->assertSame("Société 2", $experienceUpdated->getCompany(), "1.2. La société doit être identique.");
        $this->assertSame(true, $experienceUpdated->getCurrentJob(), "1.3. Le champ [currentJob] doit être identique.");
        $this->assertSame("Description 2", $experienceUpdated->getDescription(), "1.4. Le champ [description] doit être identique.");
        $this->assertSame(11, $experienceUpdated->getEndMonth(), "1.5. Le champ [endMonth] doit être identique.");
        $this->assertSame(2016, $experienceUpdated->getEndYear(), "1.6. Le champ [endYear] doit être identique.");
        $this->assertSame("Aix", $experienceUpdated->getLocation(), "1.7. Le champ [location] doit être identique.");
        $this->assertSame(2, $experienceUpdated->getStartMonth(), "1.8. Le champ [startMonth] doit être identique.");

        // Delete test.
        $id = $experienceUpdated->getId();
        static::$em->remove($experienceUpdated);
        static::$em->flush();
        static::$em->clear();
        $experienceDeleted = static::$repository->find($id);
        $this->assertNull($experienceDeleted, "3.1. L'entité doit être supprimée.");
    }

    /**
     * Insert test without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertWithoutResume() {
        $experience = new Experience();
        $experience->setCompany("Société")
                ->setDescription("Description")
                ->setEndMonth(12)
                ->setEndYear(2017)
                ->setLocation("Marseille")
                ->setStartMonth(1)
                ->setStartYear(2017);
        static::$em->persist($experience);
        static::$em->flush();
    }

}
