<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadEducation;
use Com\Nairus\UserBundle\NSUserBundle;

/**
 * Test Education Repository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var EducationRepository
     */
    private static $repository;

    // Common trait for datas cleaning.
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Education");
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();

        // Remove test fixtures.
        $this->cleanDatas(static::$container, [new LoadEducation()]);
    }

    /**
     * Test entities insert, update and delete.
     *
     * @return void
     */
    public function testInsertUpdateAndDelete(): void {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
        $newEducation = new Education();
        $newEducation
                ->setCurrentLocale("fr")
                ->setDiploma("BTS")
                ->setDomain("Informatique")
                ->setEndYear(2006)
                ->setInstitution("AFPA")
                ->setStartYear(2005)
                ->setResume($resume)
                ->setDescription("Description");
        static::$em->persist($newEducation);
        static::$em->flush();
        static::$em->clear();

        $educations = static::$repository->findAll();
        $this->assertCount(1, $educations, "1.1 Only one entity musts exist in database.");
        /* @var $education Education */
        $education = $educations[0];
        $education->setCurrentLocale("fr");
        $this->assertSame("Description", $education->getDescription(), "1.2. The [description] field has to be identical.");
        $this->assertSame("BTS", $education->getDiploma(), "1.3. The [diploma] field has to be identical.");
        $this->assertSame("Informatique", $education->getDomain(), "1.4. The [domain] field has to be identical.");
        $this->assertSame(2006, $education->getEndYear(), "1.3. The [endYear] field has to be identical.");
        $this->assertSame("AFPA", $education->getInstitution(), "1.4. The [institution] field has to be identical.");
        $this->assertSame(2005, $education->getStartYear(), "1.5. The [startYear] field has to be identical.");
        $this->assertSame($resume->getId(), $education->getResume()->getId(), "1.6. The [resume] field has to be identical.");
        $this->assertTrue($education->hasTranslation("fr"), "1.7. The entity musts have a default translation for [description] field and [fr] locale.");
        $this->assertSame("Description", $education->getDescription(), "1.8 The translation for [description] field has to be identical.");

        // Update test.
        $education
                ->setDiploma("BAC")
                ->setDomain("SI")
                ->setEndYear(1997)
                ->setInstitution("Lycée V. Hugo")
                ->setStartYear(1996)
                ->setDescription("Description 2")
        ;
        static::$em->flush();
        static::$em->clear();

        /* @var $educationUpdated Education */
        $educationUpdated = static::$repository->find($education->getId());
        $educationUpdated->setCurrentLocale("fr");
        $this->assertSame(1996, $educationUpdated->getStartYear(), "2.1. The [startYear] field has to be identical.");
        $this->assertSame("Description 2", $educationUpdated->getDescription(), "2.2. The [description] field has to be identical.");
        $this->assertSame("BAC", $educationUpdated->getDiploma(), "2.3. The [diploma] field has to be identical.");
        $this->assertSame("SI", $educationUpdated->getDomain(), "2.4. The [domain] field has to be identical.");
        $this->assertSame(1997, $educationUpdated->getEndYear(), "2.3. The [endYear] field has to be identical.");
        $this->assertSame("Lycée V. Hugo", $educationUpdated->getInstitution(), "2.4. The [institution] field has to be identical.");

        // Test translation.
        $this->assertFalse($educationUpdated->hasTranslation("en"), "3.1 The entity musts not have a [en] translation for [description] field.");
        $educationUpdated->translate("en")
                ->setDescription("Description EN")
                ->setDomain("IT");
        static::$em->flush($educationUpdated);
        static::$em->refresh($educationUpdated);
        $this->assertTrue($educationUpdated->hasTranslation("en"), "3.2 The entity musts have a [en] translation for [description] field.");
        $this->assertSame("Description EN", $educationUpdated->translate("en")->getDescription(), "3.3 The translation for [description] field has to be set correctly.");

        // Delete test
        $id = $educationUpdated->getId();
        static::$em->remove($educationUpdated);
        static::$em->flush();
        static::$em->clear();
        $educationDeleted = static::$repository->find($id);
        $this->assertNull($educationDeleted, "4.1. The entity has to be deleted.");
    }

    /**
     * Insert test without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     *
     * @return void
     */
    public function testInsertWithoutResume(): void {
        $education = new Education();
        $education
                ->setCurrentLocale("fr")
                ->setDiploma("BTS")
                ->setDomain("Informatique")
                ->setEndYear(2006)
                ->setInstitution("AFPA")
                ->setStartYear(2005)
                ->setDescription("Description")
        ;
        static::$em->persist($education);
        static::$em->flush();
    }

    /**
     * Test the `findOrderedForResumeId` method.
     *
     * @return void
     */
    public function testFindOrderedForResumeIdInFr(): void {
        // prepare test datas.
        $loadEducation = new LoadEducation();
        $loadEducation->load(static::$em);

        // get the resume with resumeSkills added.
        $author = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');
        /* @var $resume Resume */
        $resume = static::$em->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // test the method in fr
        $educationsOrderedFr = static::$repository->findOrderedForResumeId($resume->getId(), "fr");
        $educations = static::$repository->findByResume($resume);

        /* @var $firstEducation Education */
        $firstEducation = $educations[0];
        /* @var $secondEducation Education */
        $secondEducation = $educations[1];
        /* @var $thirdEducation Education */
        $thirdEducation = $educations[2];

        $this->assertCount(3, $educationsOrderedFr, "1.1 Three entities are expected in the collection.");
        $this->assertGreaterThan($firstEducation->getStartYear(), $educationsOrderedFr->get(0)->getStartYear(),
                "1.2 The start year expected has to be greater than the first education inserted in database.");
        $this->assertEquals($secondEducation->getStartYear(), $educationsOrderedFr->get(1)->getStartYear(),
                "1.3 The start year expected has to be equals than the second education inserted in database.");
        $this->assertLessThan($thirdEducation->getStartYear(), $educationsOrderedFr->get(2)->getStartYear(),
                "1.4 The start year expected has to be less than the third education inserted in database.");

        // test the translations.
        /* @var $educationTranslations \Doctrine\Common\Collections\ArrayCollection */
        $educationTranslations = $educationsOrderedFr->first()->getTranslations();
        $this->assertGreaterThan(0, $educationTranslations->count(), "2.1 At least one translation is expected.");
        $this->assertTrue($educationTranslations->containsKey("fr"), "2.2 The translation expected has to be in fr.");
    }

    /**
     * Test the `findOrderedForResumeId` method.
     *
     * @return void
     */
    public function testFindOrderedForResumeIdInEn(): void {
        // prepare test datas.
        $loadEducation = new LoadEducation();
        $loadEducation->load(static::$em);

        // get the resume with resumeSkills added.
        $author = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername('author');
        /* @var $resume Resume */
        $resume = static::$em->getRepository(NSResumeBundle::NAME . ":Resume")->findOneByAuthor($author);

        // launch the test.
        $educationsOrderedEn = static::$repository->findOrderedForResumeId($resume->getId(), "en");
        $this->assertCount(2, $educationsOrderedEn, "2.1 Two entities in en are expected in the collection");
        /* @var $educationTranslations \Doctrine\Common\Collections\ArrayCollection */
        $educationTranslations = $educationsOrderedEn->get(0)->getTranslations();
        $this->assertGreaterThan(0, $educationTranslations->count(), "2.2 At least one translation is expected.");
        $this->assertTrue($educationTranslations->containsKey("en"), "2.3 The en translation is expected in the entity.");
    }

}
