<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;

/**
 * Test of ResumeRepository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
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
        /* @var $entity Resume */
        $entity = $resumes[1];
        $this->assertCount(2, $resumes, "1.1. Two entities are expected in database.");
        $this->assertSame($newResume->getId(), $entity->getId(), "1.2. The entity id has to be the identical.");
        $this->assertSame("test", $entity->getSlug(), "1.3. The [slug] field has to be set automaticaly.");
        $this->assertTrue($entity->hasTranslation("fr", "title"), "1.4. The entity musts have a default translation for [title] field and [fr] locale.");
        $this->assertSame("Test", $entity->getTranslation("fr", "title"), "1.5 The translation for [title] field has to be identical.");
        $this->assertTrue($entity->hasTranslation("fr", "slug"), "1.6. The entity musts have a default translation for [slug] field and [fr] locale.");
        $this->assertSame("test", $entity->getTranslation("fr", "slug"), "1.7 The translation for [slug] field has to be identical.");

        // Update test.
        $entity->setAnonymous(true)
                ->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED)
                ->setTitle("Test MAJ");
        static::$em->flush();
        static::$em->clear();

        /* @var $resume Resume */
        $resume = static::$repository->find($entity->getId());
        $this->assertSame(true, $resume->getAnonymous(), "2.1. The [anomymous] fiels has to be updated.");
        $this->assertSame(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "2.2. The [status] field has to be updated.");
        $this->assertSame("Test MAJ", $resume->getTitle(), "2.3. The [title] field has to be updated.");
        $this->assertInstanceOf(\DateTimeInterface::class, $resume->getUpdatedAt(), "2.4. The update date has to be automaticaly updated.");
        $this->assertSame("Test MAJ", $resume->getTranslation("fr", "title"), "2.5 The translation for [title] field has to be updated.");
        $this->assertSame("test-maj", $resume->getTranslation("fr", "slug"), "2.6 The translation for [slug] field has to be updated.");

        // Test translations
        $this->assertFalse($resume->hasTranslation("en", "title"), "3.1. The entity musts not have a [en] translation for [title] field.");
        $this->assertFalse($resume->hasTranslation("en", "slug"), "3.2. The entity musts not have a [en] translation for [slug] field.");
        $resume->addTranslation(new ResumeTranslation("en", "title", "Title in EN", $resume))
                ->addTranslation(new ResumeTranslation("en", "slug", "Title in EN", $resume));
        static::$em->flush($resume);
        static::$em->refresh($resume);
        $this->assertTrue($resume->hasTranslation("en", "title"), "3.3. The entity musts have a [en] translation for [title] field.");
        $this->assertSame("Title in EN", $resume->getTranslation("en", "title"), "3.4. The translation in [en] for [title] field has to be identical.");
        $this->assertTrue($resume->hasTranslation("en", "slug"), "3.5. The entity musts have a [en] translation for [slug] field.");
        $this->assertSame("title-in-en", $resume->getTranslation("en", "slug"), "3.6. The translation in [en] for [slug] field has to be identical.");

        // Delete test
        $id = $resume->getId();
        static::$em->remove($resume);
        static::$em->flush();
        static::$em->clear();

        $resumeRemoved = static::$repository->find($id);
        $this->assertNull($resumeRemoved, "4.1. The entity has to be deleted.");
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
        $this->assertInstanceOf(\Doctrine\ORM\Tools\Pagination\Paginator::class, $noResumeList, "1.1. The method has to return a [Paginator] object.");
        $this->assertSame(0, $noResumeList->count(), "1.2. No resume has to be online.");

        // Add entities in the database.
        static::$loadResumeOnline->load(static::$em);

        // Page 1.
        $resumesPage1 = static::$repository->findAllOnlineForPage(1, 1);
        $this->assertSame(2, $resumesPage1->count(), "2.1. Only two resumes have to be online.");
        $resultPage1 = $resumesPage1->getQuery()->getResult();
        $this->assertCount(1, $resultPage1, "2.2. One resume has to be on page 1.");
        $this->assertSame("Test1", $resultPage1[0]->getTitle(), "2.3. The first resume has to have the title expected.");

        // Page 2.
        $resumesPage2 = static::$repository->findAllOnlineForPage(2, 1);
        $this->assertSame(2, $resumesPage2->count(), "3.1. Only two resumes have to be online.");
        $resultPage2 = $resumesPage2->getQuery()->getResult();
        $this->assertCount(1, $resultPage2, "3.2. One resume has to be on  page 2.");
        $this->assertSame("Test0", $resultPage2[0]->getTitle(), "3.3. The second resume has to have the title expected.");

        // Page 3.
        $resumesPage3 = static::$repository->findAllOnlineForPage(3, 1);
        $this->assertSame(2, $resumesPage3->count(), "3.1. Only two resumes have to be online.");
        $resultPage3 = $resumesPage3->getQuery()->getResult();
        $this->assertCount(0, $resultPage3, "3.2. No resume are expected on page 3.");

        // Delete datas for others tests.
        static::$loadResumeOnline->remove(static::$em);
    }

    /**
     * Test find a resume with his translation.
     *
     * @covers ResumeRepository::findWithTranslation
     */
    public function testFindWithTranslation() {
        /* @var $resumeFr Resume */
        $resumeFr = static::$repository->findWithTranslation(1, "fr");
        $this->assertInstanceOf("Com\Nairus\ResumeBundle\Entity\Resume", $resumeFr, "1. The entity has to be an instance of [Resume] with [fr] translation.");

        $resumeEn = static::$repository->findWithTranslation(1, "en");
        $this->assertNull($resumeEn, "2. The resume musts not exist with [en] locale.");
    }

}
