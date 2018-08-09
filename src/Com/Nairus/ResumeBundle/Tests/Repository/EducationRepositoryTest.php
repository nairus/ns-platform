<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Tests\AbstractKernelTestCase;

/**
 * Test de la classe EducationRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class EducationRepositoryTest extends AbstractKernelTestCase
{

    /**
     * @var EducationRepository
     */
    private static $repository;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Education");
    }

    /**
     * Test d'insertion, de mise à jour et de suppression de l'entité.
     */
    public function testInsertUpdateAndDelete()
    {
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
        $this->assertCount(1, $educations, "1.1 Il doit y avoir une entité en base.");
        /* @var $education Education */
        $education = $educations[0];
        $this->assertSame("Description", $education->getDescription(), "1.2. Le champ [description] doit être identique.");
        $this->assertSame("BTS", $education->getDiploma(), "1.3. Le champ [diploma] doit être identique.");
        $this->assertSame("Informatique", $education->getDomain(), "1.4. Le champ [domain] doit être identique.");
        $this->assertSame(2006, $education->getEndYear(), "1.3. Le champ [endYear] doit être identique.");
        $this->assertSame("AFPA", $education->getInstitution(), "1.4. Le champ [institution] doit être identique.");
        $this->assertSame(2005, $education->getStartYear(), "1.5. Le champ [startYear] doit être identique.");
        $this->assertSame($resume->getId(), $education->getResume()->getId(), "1.6. Le champ [resume] doit être identique.");

        // Test de mise à jour.
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
        $this->assertSame(1996, $educationUpdated->getStartYear(), "2.1. Le champ [startYear] doit être identique.");
        $this->assertSame("Description 2", $educationUpdated->getDescription(), "2.2. Le champ [description] doit être identique.");
        $this->assertSame("BAC", $educationUpdated->getDiploma(), "2.3. Le champ [diploma] doit être identique.");
        $this->assertSame("SI", $educationUpdated->getDomain(), "2.4. Le champ [domain] doit être identique.");
        $this->assertSame(1997, $educationUpdated->getEndYear(), "2.3. Le champ [endYear] doit être identique.");
        $this->assertSame("Lycée V. Hugo", $educationUpdated->getInstitution(), "2.4. Le champ [institution] doit être identique.");

        // Test de suppression.
        $id = $educationUpdated->getId();
        static::$em->remove($educationUpdated);
        static::$em->flush();
        static::$em->clear();
        $educationDeleted = static::$repository->find($id);
        $this->assertNull($educationDeleted, "3.1. L'entité doit être supprimée.");
    }

    /**
     * Test d'insertion d'une entité sans sa clé étrangère.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertWithoutResume()
    {
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
