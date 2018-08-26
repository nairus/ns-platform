<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation;
use Com\Nairus\ResumeBundle\Entity\Resume;

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

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Education");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $resume Resume */
        $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
        $newEducation = new Education();
        $newEducation
                ->setDescription("Description")
                ->setDiploma("BTS")
                ->setDomain("Informatique")
                ->setEndYear(2006)
                ->setInstitution("AFPA")
                ->setStartYear(2005)
                ->setResume($resume);
        static::$em->persist($newEducation);
        static::$em->flush();
        static::$em->clear();

        $educations = static::$repository->findAll();
        $this->assertCount(1, $educations, "1.1 Only one entity musts exist in database.");
        /* @var $education Education */
        $education = $educations[0];
        $this->assertSame("Description", $education->getDescription(), "1.2. The [description] field has to be identical.");
        $this->assertSame("BTS", $education->getDiploma(), "1.3. The [diploma] field has to be identical.");
        $this->assertSame("Informatique", $education->getDomain(), "1.4. The [domain] field has to be identical.");
        $this->assertSame(2006, $education->getEndYear(), "1.3. The [endYear] field has to be identical.");
        $this->assertSame("AFPA", $education->getInstitution(), "1.4. The [institution] field has to be identical.");
        $this->assertSame(2005, $education->getStartYear(), "1.5. The [startYear] field has to be identical.");
        $this->assertSame($resume->getId(), $education->getResume()->getId(), "1.6. The [resume] field has to be identical.");
        $this->assertTrue($education->hasTranslation("fr", "description"), "1.7. The entity musts have a default translation for [description] field and [fr] locale.");
        $this->assertSame("Description", $education->getTranslation("fr", "description"), "1.8 The translation for [description] field has to be identical.");

        // Update test.
        $education->setDescription("Description 2")
                ->setDiploma("BAC")
                ->setDomain("SI")
                ->setEndYear(1997)
                ->setInstitution("Lycée V. Hugo")
                ->setStartYear(1996);
        static::$em->flush();
        static::$em->clear();

        /* @var $educationUpdated Education */
        $educationUpdated = static::$repository->find($education->getId());
        $this->assertSame(1996, $educationUpdated->getStartYear(), "2.1. The [startYear] field has to be identical.");
        $this->assertSame("Description 2", $educationUpdated->getDescription(), "2.2. The [description] field has to be identical.");
        $this->assertSame("BAC", $educationUpdated->getDiploma(), "2.3. The [diploma] field has to be identical.");
        $this->assertSame("SI", $educationUpdated->getDomain(), "2.4. The [domain] field has to be identical.");
        $this->assertSame(1997, $educationUpdated->getEndYear(), "2.3. The [endYear] field has to be identical.");
        $this->assertSame("Lycée V. Hugo", $educationUpdated->getInstitution(), "2.4. The [institution] field has to be identical.");
        $this->assertSame("Description 2", $educationUpdated->getTranslation("fr", "description"), "2.5 The translation for [description] field has to be identical.");

        // Test translation.
        $this->assertFalse($educationUpdated->hasTranslation("en", "description"), "3.1 The entity musts not have a [en] translation for [description] field.");
        $educationUpdated->addTranslation(new EducationTranslation("en", "description", "Description EN", $educationUpdated));
        static::$em->flush($educationUpdated);
        static::$em->refresh($educationUpdated);
        $this->assertTrue($educationUpdated->hasTranslation("en", "description"), "3.2 The entity musts have a [en] translation for [description] field.");
        $this->assertSame("Description EN", $educationUpdated->getTranslation("en", "description"), "3.3 The translation for [description] field has to be set correctly.");

        // Delete test
        $id = $educationUpdated->getId();
        static::$em->remove($educationUpdated);
        static::$em->flush();
        static::$em->clear();
        $educationDeleted = static::$repository->find($id);
        $this->assertNull($educationDeleted, "3.1. The entity has to be deleted.");
    }

    /**
     * Insert test without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertWithoutResume() {
        $education = new Education();
        $education
                ->setDescription("Description")
                ->setDiploma("BTS")
                ->setDomain("Informatique")
                ->setEndYear(2006)
                ->setInstitution("AFPA")
                ->setStartYear(2005);
        static::$em->persist($education);
        static::$em->flush();
    }

}
