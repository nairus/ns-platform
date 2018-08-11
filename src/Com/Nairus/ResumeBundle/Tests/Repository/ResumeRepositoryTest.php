<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;

/**
 * Test de la classe ResumeRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ResumeRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ResumeRepository
     */
    private static $repository;

    /**
     * Users manager.
     *
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    private static $userManager;

    /**
     * Class to load online resume's fixtures.
     *
     * @var LoadResumeOnline
     */
    private static $loadResumeOnline;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Resume");
        static::$userManager = static::$container->get("fos_user.user_manager");
        static::$loadResumeOnline = new LoadResumeOnline(static::$userManager);
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        /* @var $author UserInterface */
        $author = static::$userManager->findUserByUsername("author");
        $newResume = new Resume();
        $newResume->setIp("127.0.0.1")
                ->setTitle("Test")
                ->setAuthor($author);
        static::$em->persist($newResume);
        static::$em->flush();
        static::$em->clear();

        // Get the resumes.
        $resumes = static::$repository->findAll();
        $this->assertCount(2, $resumes, "1.1. Il doit y avoir 2 entités en base.");
        $this->assertSame($newResume->getId(), $resumes[1]->getId(), "1.2. L'id de l'entité récupérée doit être identique à celle créée.");

        // Update test.
        /* @var $entity Resume */
        $entity = $resumes[1];
        $entity->setAnonymous(true)
                ->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED)
                ->setTitle("Test MAJ");
        static::$em->flush();
        static::$em->clear();

        /* @var $resume Resume */
        $resume = static::$repository->find($entity->getId());
        $this->assertSame(true, $resume->getAnonymous(), "2.1. Le champ [anomymous] doit être mise à jour.");
        $this->assertSame(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "2.2. Le champ [status] doit être mise à jour.");
        $this->assertSame("Test MAJ", $resume->getTitle(), "2.3. Le champ [title] doit être mise à jour.");
        $this->assertInstanceOf(\DateTimeInterface::class, $resume->getUpdateDate(), "2.4. La date de mise à jour doit automatiquement définie.");

        // Delete test
        $id = $resume->getId();
        static::$em->remove($resume);
        static::$em->flush();
        static::$em->clear();

        $resumeRemoved = static::$repository->find($id);
        $this->assertNull($resumeRemoved, "3.1. L'entité doit être supprimée.");
    }

    /**
     * Insert test without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertResumeWithoutAuthor() {
        $resume = new Resume();
        $resume->setIp("127.0.0.1")
                ->setTitle("Test");
        static::$em->persist($resume);
        static::$em->flush();
    }

    /**
     * Test getting paginated entities.
     *
     * @covers ResumeRepository::findAllOnlineForPage
     */
    public function testFindAllOnlineForPage() {
        // Test with no online resume.
        $noResumeList = static::$repository->findAllOnlineForPage(1, 1);
        $this->assertInstanceOf(\Doctrine\ORM\Tools\Pagination\Paginator::class, $noResumeList, "1.1. La méthode doit retourner un objet de type [Paginator].");
        $this->assertSame(0, $noResumeList->count(), "1.2. Il ne doit y avoir aucun CV en ligne.");

        // Add entities in the database.
        static::$loadResumeOnline->load(static::$em);

        // Page 1.
        $resumesPage1 = static::$repository->findAllOnlineForPage(1, 1);
        $this->assertSame(2, $resumesPage1->count(), "2.1. Il ne doit y avoir que 2 CV en ligne sur toutes les pages.");
        $resultPage1 = $resumesPage1->getQuery()->getResult();
        $this->assertCount(1, $resultPage1, "2.2. Il doit y avoir un CV sur la page 1.");
        $this->assertSame("Test1", $resultPage1[0]->getTitle(), "2.3. Le premier CV doit avoir le titre attendu.");

        // Page 2.
        $resumesPage2 = static::$repository->findAllOnlineForPage(2, 1);
        $this->assertSame(2, $resumesPage2->count(), "3.1. Il ne doit y avoir que 2 CV en ligne sur toutes les pages.");
        $resultPage2 = $resumesPage2->getQuery()->getResult();
        $this->assertCount(1, $resultPage2, "3.2. Il doit y avoir un CV sur la page 2.");
        $this->assertSame("Test0", $resultPage2[0]->getTitle(), "3.3. Le deuxième CV doit avoir le titre attendu.");

        // Page 3.
        $resumesPage3 = static::$repository->findAllOnlineForPage(3, 1);
        $this->assertSame(2, $resumesPage3->count(), "3.1. Il ne doit y avoir que 2 CV en ligne sur toutes les pages.");
        $resultPage3 = $resumesPage3->getQuery()->getResult();
        $this->assertCount(0, $resultPage3, "3.2. Il doit y avoir aucun CV sur la page 3.");

        // Delete datas for others tests.
        static::$loadResumeOnline->remove(static::$em);
    }

}
