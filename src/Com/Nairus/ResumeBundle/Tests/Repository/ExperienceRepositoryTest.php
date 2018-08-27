<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation;

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
        $this->assertCount(1, $experiences, "1.1. Only one entity musts exist in database.");
        /* @var $experience Experience */
        $experience = $experiences[0];
        $this->assertSame($newExperience->getCompany(), $experience->getCompany(), "1.2. The company has to be identical.");
        $this->assertSame($newExperience->getCurrentJob(), $experience->getCurrentJob(), "1.3. The [currentJob] field has to be identical.");
        $this->assertSame($newExperience->getDescription(), $experience->getDescription(), "1.4. The [description] field has to be identical.");
        $this->assertSame($newExperience->getEndMonth(), $experience->getEndMonth(), "1.5. The [endMonth] field has to be identical.");
        $this->assertSame($newExperience->getEndYear(), $experience->getEndYear(), "1.6. The [endYear] field has to be identical.");
        $this->assertSame($newExperience->getLocation(), $experience->getLocation(), "1.7. The [location] field has to be identical.");
        $this->assertSame($newExperience->getResume()->getId(), $experience->getResume()->getId(), "1.8. The [resume] field has to be identical.");
        $this->assertSame($newExperience->getStartMonth(), $experience->getStartMonth(), "1.9. The [startMonth] field has to be identical.");
        $this->assertSame($newExperience->getStartYear(), $experience->getStartYear(), "1.10. The [startYear] field has to be identical.");
        $this->assertTrue($experience->hasTranslation("fr", "description"), "1.11. The entity musts have a [fr] translation for [description] field.");
        $this->assertSame("Description", $experience->getTranslation("fr", "description"), "1.12. The translation has to be identical to the default description.");

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
        $this->assertSame(2016, $experienceUpdated->getStartYear(), "2.1. The [startYear] field has to be identical.");
        $this->assertSame("Société 2", $experienceUpdated->getCompany(), "2.2. La société field has to be identical.");
        $this->assertSame(true, $experienceUpdated->getCurrentJob(), "2.3. The [currentJob] field has to be identical.");
        $this->assertSame("Description 2", $experienceUpdated->getDescription(), "1.4. The [description] field has to be identical.");
        $this->assertSame(11, $experienceUpdated->getEndMonth(), "2.5. The [endMonth] field has to be identical.");
        $this->assertSame(2016, $experienceUpdated->getEndYear(), "2.6. The [endYear] field has to be identical.");
        $this->assertSame("Aix", $experienceUpdated->getLocation(), "2.7. The [location] field has to be identical.");
        $this->assertSame(2, $experienceUpdated->getStartMonth(), "2.8. The [startMonth] field has to be identical.");
        $this->assertSame("Description 2", $experienceUpdated->getTranslation("fr", "description"), "2.9 The default translation for [description] field has to be updated.");

        // Test translation.
        $this->assertFalse($experienceUpdated->hasTranslation("en", "description"), "3.1 The entity musts not have a [en] translation for [description] field.");
        $experienceUpdated->addTranslation(new ExperienceTranslation("en", "description", "Description EN", $experienceUpdated));
        static::$em->flush($experienceUpdated);
        static::$em->refresh($experienceUpdated);
        $this->assertTrue($experienceUpdated->hasTranslation("en", "description"), "3.2 The entity musts have a [en] translation for [description] field.");
        $this->assertSame("Description EN", $experienceUpdated->getTranslation("en", "description"), "3.3 The translation for [description] field has to be set correctly.");

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
