<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\Constants\ExceptionCodeConstants;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Exception as NSResumeException;
use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\ResumeBundle\Dto\ResumePaginatorDto;

/**
 * Test of ResumeService.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeServiceTest extends AbstractKernelTestCase {

    /**
     * Service of resumes.
     *
     * @var ResumeService
     */
    protected $object;

    /**
     * Id of the resume to remove after a test.
     *
     * @var int
     */
    private $resumeToRemove;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        // Load test fixtures.
        static::loadBeforeClass(static::$em, [new LoadSkill(), new LoadSkillLevel()]);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass() {
        // Remove test fixtures.
        $loadSkill = new LoadSkill();
        $loadSkill->remove(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->remove(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        // Insert online resumes.
        $this->object = new ResumeService(static::$em);
        $this->loadResumeOnline = new LoadResumeOnline();
        $this->loadResumeOnline->load(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // Reset status of the first resume.
        /* @var $resume Resume */
        $resume = static::$em->find(Resume::class, 1);
        $resume->setStatus(ResumeStatusEnum::OFFLINE_INCOMPLETE)
                ->setAnonymous(false);
        static::$em->flush($resume);

        // Remove online resumes.
        $this->loadResumeOnline->remove(static::$em);

        if (null !== $this->resumeToRemove) {
            $resumeToRemove = static::$em->find(Resume::class, $this->resumeToRemove);
            static::$em->remove($resumeToRemove);
            static::$em->flush($resumeToRemove);
        }
    }

    /**
     * Test the implementations of the service.
     *
     * @return void
     */
    public function testImplementations(): void {
        $this->assertInstanceOf(ResumeServiceInterface::class, $this->object, "1. The service has to implement [ResumeServiceInterface].");
    }

    /**
     * Test <code>findAllOnlineForPage</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::findAllOnlineForPage
     *
     * @return void
     */
    public function testFindAllOnlineForPage(): void {
        try {
            $resumeDto = $this->object->findAllOnlineForPage(1, 2, "fr");
            $this->assertInstanceOf(ResumePaginatorDto::class, $resumeDto, "1. The result expected has to be an instance of [ResumePaginatorDto]");
            $this->assertSame(1, $resumeDto->getPages(), "2. One page has to be displayed only.");
            $this->assertCount(2, $resumeDto->getEntities(), "3. Two entities are expexted on the first page.");
        } catch (\Exception | \Error $exc) {
            $this->fail("No exception/error has to be thrown: " . $exc->getMessage());
        }
    }

    /**
     * Test <code>findAllOnlineForPage</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::findAllOnlineForPage
     *
     * @return void
     */
    public function testFindAllOnlineForPageWithWrongPage(): void {
        try {
            $this->object->findAllOnlineForPage(0, 50, "fr");
        } catch (NSResumeException\ResumeListException $exc) {
            $this->assertSame(0, $exc->getPage(), "1. The page number expected is not ok.");
            $this->assertSame(ExceptionCodeConstants::WRONG_PAGE, $exc->getCode(), "2. The exception code expected is not ok.");
        } catch (\Exception | \Error $exc) {
            $this->fail("Wrong exception/error expected: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::findAllOnlineForPage
     */
    public function testFindAllOnlineForPageWithPageNotExists(): void {
        try {
            $this->object->findAllOnlineForPage(2, 50, "fr");
        } catch (NSResumeException\ResumeListException $exc) {
            $this->assertSame(2, $exc->getPage(), "1. The page number expected is not ok.");
            $this->assertSame(ExceptionCodeConstants::PAGE_NOT_FOUND, $exc->getCode(), "2. The exception code expected is not ok.");
        } catch (\Exception | \Error $exc) {
            $this->fail("Wrong exception/error expected: " . $exc->getMessage());
        }
    }

    /**
     * Test the instanciation by the ioc.
     */
    public function testLoadWithIoc(): void {
        try {
            $resumeService = static::$container->get("ns_resume.resume_service");
            $this->assertInstanceOf(ResumeServiceInterface::class, $resumeService, "1. The service has to implement [ResumeServiceInterface] interface.");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception/error not expected: " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>removeWithDependencies</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::removeWithDependencies
     *
     * @return void
     */
    public function testRemoveWithDependencies(): void {
        /* @var $skill Skill */
        $skill = static::$em->getRepository(Skill::class)->findOneBy(["title" => "PHP 7"]);
        /* @var $skillLevel SkillLevel */
        $skillLevel = static::$em->getRepository(SkillLevel::class)->findAll()[0];
        /* @var $user User */
        $user = static::$em->getRepository(User::class)->findOneBy(["username" => "user"]);

        // Prepare all datas for test.
        $resume = new Resume();
        $education = new Education();
        $experience = new Experience();
        $resumeSkill = new ResumeSkill();
        $education
                ->setCurrentLocale("fr")
                ->setDiploma("Diplome")
                ->setDomain("Domaine")
                ->setEndYear(2017)
                ->setInstitution("Institution")
                ->setResume($resume)
                ->setStartYear(2016)
                ->setDescription("Description");
        $experience
                ->setCompany("Société")
                ->setCurrentJob(true)
                ->setCurrentLocale("fr")
                ->setEndMonth(1)
                ->setEndYear(2018)
                ->setLocation("Marseille")
                ->setResume($resume)
                ->setStartMonth(12)
                ->setStartYear(2017)
                ->setDescription("Description");
        $resumeSkill->setRank(1)
                ->setResume($resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);
        $resume->setCurrentLocale('fr')
                ->setAuthor($user)
                ->setIp("127.0.0.0")
                ->setTitle('Foo Bar');

        // Load datas.
        $this->loadDatas(static::$em, [$resume, $education, $experience, $resumeSkill]);

        // Refresh the entity.
        static::$em->refresh($resume);
        static::$em->refresh($education);
        static::$em->refresh($experience);
        static::$em->refresh($resumeSkill);

        $resumeId = $resume->getId();
        $educationId = $education->getId();
        $experienceId = $experience->getId();
        $resumeSkillId = $resumeSkill->getId();
        $this->object->removeWithDependencies($resume);

        $this->assertNull(static::$em->find(Resume::class, $resumeId), "1. The resume entity has to be removed");
        $this->assertNull(static::$em->find(Education::class, $educationId), "2. The education entity has to be removed");
        $this->assertNull(static::$em->find(Experience::class, $experienceId), "3. The experience entity has to be removed");
        $this->assertNull(static::$em->find(ResumeSkill::class, $resumeSkillId), "4. The resume skill entity has to be removed");
    }

    /**
     * Test the <code>removeWithDependencies</code> method with error.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::removeWithDependencies
     *
     * @return void
     */
    public function testRemoveWithDependenciesAndErrors(): void {
        // Create the mocks for the test.
        $resume = $this->createMock(Resume::class);
        $resume->expects($this->at(0))
                ->method('getId')
                ->willReturn(1);
        $resume->expects($this->at(1))
                ->method('getResumeSkills')
                ->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $resume->expects($this->at(2))
                ->method('getEducations')
                ->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $resume->expects($this->at(3))
                ->method('getExperiences')
                ->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        $resumeSkillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager
                ->expects($this->at(0))
                ->method('getRepository')
                ->willReturn($resumeSkillRepository);
        $objectManager
                ->expects($this->at(1))
                ->method('beginTransaction')
                ->willReturn(null);
        $objectManager->expects($this->at(2))
                ->method('remove')
                ->willThrowException(new \Exception("Error"));
        $objectManager
                ->expects($this->at(3))
                ->method('rollback')
                ->willReturn(null);

        // The test
        try {
            $service = new ResumeService($objectManager);
            $service->removeWithDependencies($resume);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.resume.delete", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Error | \Exception $exc) {
            $this->fail("2. Unexpected exception! " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>publish</code> method for nominal case.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::publish
     *
     * @return void
     */
    public function testPublish(): void {
        // Find the first resume added in the fixture (anonymous, with profile).
        /* @var $resume Resume */
        $resume = static::$em->find(Resume::class, 1);
        $resume->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED);
        $resume->setAnonymous(true);
        $this->object->publish($resume);
        static::$em->refresh($resume);
        $this->assertEquals(ResumeStatusEnum::ONLINE, $resume->getStatus(), "1. The resume has to be online.");
    }

    /**
     * Test the <code>publish</code> method with force case.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::publish
     *
     * @return void
     */
    public function testPublishWithForce(): void {
        // Find the first resume added in the fixture (anonymous, with profile).
        /* @var $resume Resume */
        $resume = static::$em->find(Resume::class, 1);
        $this->object->publish($resume, true);
        static::$em->refresh($resume);
        $this->assertEquals(ResumeStatusEnum::ONLINE, $resume->getStatus(), "1. The resume has to be online.");
    }

    /**
     * Test the <code>publish</code> method with resume already online.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::publish
     *
     * @return void
     */
    public function testPublishWithResumeAlreadyOnline(): void {
        try {
            /* @var $resume Resume */
            $resume = static::$em->getRepository(Resume::class)->findOneBy(['status' => ResumeStatusEnum::ONLINE]);
            $this->object->publish($resume);
            $this->fail("1. [ResumePublicationException] expected!");
        } catch (NSResumeException\ResumePublicationException $exc) {
            $this->assertEquals("flashes.error.resume.already-published", $exc->getTranslationKey(), "2. The key expected is not ok.");
        } catch (\Throwable $exc) {
            $this->fail("3. Unexpected exception: " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>publish</code> method with resume incomplete.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::publish
     *
     * @return void
     */
    public function testPublishWithResumeIncomplete(): void {
        try {
            // Find the first resume added in the fixture (anonymous, with profile).
            /* @var $resume Resume */
            $resume = static::$em->find(Resume::class, 1);
            $this->object->publish($resume);
            $this->fail("1. [ResumePublicationException] expected!");
        } catch (NSResumeException\ResumeIncompleteException $exc) {
            $this->assertEquals("flashes.error.resume.incomplete", $exc->getTranslationKey(), "2. The key expected is not ok.");
        } catch (\Throwable $exc) {
            $this->fail("3. Unexpected exception: " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>publish</code> method with resume not anonymous and no user profile.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::publish
     *
     * @return void
     */
    public function testPublishWithNotAnonymousAndNoProfile(): void {
        /* @var $user User */
        $user = static::$em->getRepository(User::class)->findOneBy(["username" => "user"]);

        $resume = new Resume();
        $resume->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED)
                ->setIp("0")
                ->setAuthor($user);
        $this->loadDatas(static::$em, [$resume]);
        $this->resumeToRemove = $resume->getId();

        // Try to publish resume.
        try {
            $this->object->publish($resume);
            $this->fail("1. [ResumePublicationException] expected!");
        } catch (NSResumeException\ResumePublicationException $exc) {
            $this->assertEquals("flashes.error.resume.no-profile", $exc->getTranslationKey(), "2. The key expected is not ok.");
        } catch (\Throwable $exc) {
            $this->fail("3. Unexpected exception: " . $exc->getMessage());
        }
    }

    /**
     * Test the <code>unpublish</code> method with an incomplete resume.
     *
     * @return void
     */
    public function testUnpublishWithIncompleteResume(): void {
        /* @var $resume Resume */
        $resume = static::$em->getRepository(Resume::class)->findOneBy(['status' => ResumeStatusEnum::ONLINE]);

        // Remove all resume skills.
        $resumeSkills = $resume->getResumeSkills();
        foreach ($resumeSkills as /* @var $resumeSkill ResumeSkill */ $resumeSkill) {
            $resume->removeResumeSkill($resumeSkill);
            static::$em->remove($resumeSkill);
        }
        static::$em->flush();

        $this->assertCount(0, $resume->getResumeSkills(), "1. No resume skill has to remain in the collection.");

        $this->object->unpublish($resume);
        static::$em->refresh($resume);

        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $resume->getStatus(), "2. The status expected is not ok.");
    }

    /**
     * Test the <code>unpublish</code> method with an complete resume.
     *
     * @return void
     */
    public function testUnpublishWithCompleteResume(): void {
        /* @var $resume Resume */
        $resume = static::$em->getRepository(Resume::class)->findOneBy(['status' => ResumeStatusEnum::ONLINE]);

        $this->object->unpublish($resume);
        static::$em->refresh($resume);

        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "1. The status expected is not ok.");
    }

}
