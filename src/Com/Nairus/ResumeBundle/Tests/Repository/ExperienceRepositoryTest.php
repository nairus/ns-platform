<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadExperience;
use Com\Nairus\UserBundle\NSUserBundle;

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

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Experience");
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();

        // Remove test fixtures.
        $this->cleanDatas(static::$container, [new LoadExperience()]);
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);

        $newExperience = new Experience();
        $newExperience
                ->setCurrentLocale("fr")
                ->setCompany("Société")
                ->setEndMonth(12)
                ->setEndYear(2017)
                ->setLocation("Marseille")
                ->setStartMonth(1)
                ->setStartYear(2017)
                ->setResume($resume)
                ->setDescription("Description")
        ;
        static::$em->persist($newExperience);
        static::$em->flush();
        static::$em->clear();

        $experiences = static::$repository->findAll();
        $this->assertCount(1, $experiences, "1.1. Only one entity musts exist in database.");
        /* @var $experience Experience */
        $experience = $experiences[0];
        $experience->setCurrentLocale("fr");
        $this->assertSame($newExperience->getCompany(), $experience->getCompany(), "1.2. The company has to be identical.");
        $this->assertSame($newExperience->getCurrentJob(), $experience->getCurrentJob(), "1.3. The [currentJob] field has to be identical.");
        $this->assertSame($newExperience->getDescription(), $experience->getDescription(), "1.4. The [description] field has to be identical.");
        $this->assertSame($newExperience->getEndMonth(), $experience->getEndMonth(), "1.5. The [endMonth] field has to be identical.");
        $this->assertSame($newExperience->getEndYear(), $experience->getEndYear(), "1.6. The [endYear] field has to be identical.");
        $this->assertSame($newExperience->getLocation(), $experience->getLocation(), "1.7. The [location] field has to be identical.");
        $this->assertSame($newExperience->getResume()->getId(), $experience->getResume()->getId(), "1.8. The [resume] field has to be identical.");
        $this->assertSame($newExperience->getStartMonth(), $experience->getStartMonth(), "1.9. The [startMonth] field has to be identical.");
        $this->assertSame($newExperience->getStartYear(), $experience->getStartYear(), "1.10. The [startYear] field has to be identical.");
        $this->assertTrue($experience->hasTranslation("fr"), "1.11. The entity musts have a [fr] translation for [description] field.");
        $this->assertSame("Description", $experience->getDescription(), "1.12. The translation has to be identical to the default description.");

        // Update test.
        $experience
                ->setCurrentLocale("fr")
                ->setCompany("Société 2")
                ->setLocation("Aix")
                ->setStartMonth(2)
                ->setStartYear(2016)
                ->setCurrentJob(true)
                ->setDescription("Description 2")
        ;
        static::$em->flush();
        static::$em->clear();
        /* @var $experienceUpdated Experience */
        $experienceUpdated = static::$repository->find($experience->getId());
        $experienceUpdated->setCurrentLocale("fr");
        $this->assertSame(2016, $experienceUpdated->getStartYear(), "2.1. The [startYear] field has to be identical.");
        $this->assertSame("Société 2", $experienceUpdated->getCompany(), "2.2. La société field has to be identical.");
        $this->assertSame(true, $experienceUpdated->getCurrentJob(), "2.3. The [currentJob] field has to be identical.");
        $this->assertSame("Description 2", $experienceUpdated->getDescription(), "1.4. The [description] field has to be identical.");
        $this->assertNull($experienceUpdated->getEndMonth(), "2.5. The [endMonth] field has to be null.");
        $this->assertNull($experienceUpdated->getEndYear(), "2.6. The [endYear] field has to be null.");
        $this->assertSame("Aix", $experienceUpdated->getLocation(), "2.7. The [location] field has to be identical.");
        $this->assertSame(2, $experienceUpdated->getStartMonth(), "2.8. The [startMonth] field has to be identical.");
        $this->assertSame("Description 2", $experienceUpdated->getDescription(), "2.9 The default translation for [description] field has to be updated.");

        // Test translation.
        $this->assertFalse($experienceUpdated->hasTranslation("en", "description"), "3.1 The entity musts not have a [en] translation for [description] field.");
        $experienceUpdated->translate("en")->setDescription("Description EN");
        static::$em->flush($experienceUpdated);
        static::$em->refresh($experienceUpdated);
        $this->assertTrue($experienceUpdated->hasTranslation("en"), "3.2 The entity musts have a [en] translation for [description] field.");
        $this->assertSame("Description EN", $experienceUpdated->translate("en")->getDescription(), "3.3 The translation for [description] field has to be set correctly.");

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
        $experience
                ->setCurrentLocale("fr")
                ->setCompany("Société")
                ->setEndMonth(12)
                ->setEndYear(2017)
                ->setLocation("Marseille")
                ->setStartMonth(1)
                ->setStartYear(2017)
                ->setDescription("Description")
        ;
        static::$em->persist($experience);
        static::$em->flush();
    }

    /**
     * Test the <code>findOrderedForResumeId</code> method.
     *
     * @return void
     */
    public function testFindOrderedForResumeIdInFr(): void {
        // prepare test datas.
        $loadEducation = new LoadExperience();
        $loadEducation->load(static::$em);

        // get the resume with resumeSkills added.
        $author = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');
        /* @var $resume Resume */
        $resume = static::$em->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // test in fr.
        $experiencesOrderedFr = static::$repository->findOrderedForResumeId($resume->getId(), "fr");

        $this->assertCount(3, $experiencesOrderedFr, "1.1 Three entities fr are expected in the collection.");
        $this->assertGreaterThan($experiencesOrderedFr->get(1)->getStartYear(), $experiencesOrderedFr->get(0)->getStartYear(),
                "1.2 The start year of the first entity has to be greater than the second.");
        $this->assertEquals($experiencesOrderedFr->get(1)->getStartYear(), $experiencesOrderedFr->get(2)->getStartYear(),
                "1.3 The start year of the third entity has to be equal than the second.");
        $this->assertLessThan($experiencesOrderedFr->get(1)->getStartMonth(), $experiencesOrderedFr->get(2)->getStartMonth(),
                "1.4 The start month of the third entity has to be less than the second.");

        // verify fr translation.
        /* @var $experienceTranslations \Doctrine\Common\Collections\ArrayCollection */
        $experienceTranslations = $experiencesOrderedFr->get(0)->getTranslations();
        $this->assertGreaterThan(0, $experienceTranslations->count(), "2.1 At least one translation is expected in the entity.");
        $this->assertTrue($experienceTranslations->containsKey("fr"), "2.2 The fr translation is expected in the entity.");
    }

    /**
     * Test the <code>findOrderedForResumeId</code> method.
     *
     * @return void
     */
    public function testFindOrderedForResumeIdEn(): void {
        // prepare test datas.
        $loadEducation = new LoadExperience();
        $loadEducation->load(static::$em);

        // get the resume with resumeSkills added.
        $author = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');
        /* @var $resume Resume */
        $resume = static::$em->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // test in en.
        $experiencesOrderedEn = static::$repository->findOrderedForResumeId($resume->getId(), "en");
        $this->assertCount(2, $experiencesOrderedEn, "1.1 Two entities en are expected in the collection.");
        /* @var $experienceTranslations \Doctrine\Common\Collections\ArrayCollection */
        $experienceTranslations = $experiencesOrderedEn->get(0)->getTranslations();
        $this->assertGreaterThan(0, $experienceTranslations->count(), "1.2 At least one translation is expected in the entity.");
        $this->assertTrue($experienceTranslations->containsKey("en"), "1.3 The en translation is expected in the entity.");
    }

}
