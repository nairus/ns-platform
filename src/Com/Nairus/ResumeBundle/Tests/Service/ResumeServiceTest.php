<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\Constants\ExceptionCodeConstants;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Profile;
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
     * Test `findAllOnlineForPage` method.
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
     * Test `findAllOnlineForPage` method.
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
     * Test the `removeWithDependencies` method.
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

        $resumeId = $resume->getId();
        $educationId = $education->getId();
        $experienceId = $experience->getId();
        $resumeSkillId = $resumeSkill->getId();
        $this->object->removeWithDependencies(static::$em->find(Resume::class, $resumeId));

        $this->assertNull(static::$em->find(Resume::class, $resumeId), "1. The resume entity has to be removed");
        $this->assertNull(static::$em->find(Education::class, $educationId), "2. The education entity has to be removed");
        $this->assertNull(static::$em->find(Experience::class, $experienceId), "3. The experience entity has to be removed");
        $this->assertNull(static::$em->find(ResumeSkill::class, $resumeSkillId), "4. The resume skill entity has to be removed");
    }

    /**
     * Test the `removeWithDependencies` method with error.
     *
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::removeWithDependencies
     *
     * @return void
     */
    public function testRemoveWithDependenciesAndErrors(): void {
        // Create the mocks for the test.
        $resume = $this->createMock(Resume::class);
        $resume->expects($this->exactly(1))
                ->method('getId')
                ->willReturn(1);

        $repositoryMock = $this->createMock(\Doctrine\Common\Persistence\ObjectRepository::class);
        $repositoryMock->expects($this->exactly(3))
                ->method('findBy')
                ->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager
                ->expects($this->exactly(5))
                ->method('getRepository')
                ->willReturn($repositoryMock);
        $objectManager
                ->expects($this->exactly(1))
                ->method('beginTransaction')
                ->willReturn(null);
        $objectManager->expects($this->exactly(1))
                ->method('remove')
                ->willThrowException(new \Doctrine\ORM\ORMException("Error"));
        $objectManager
                ->expects($this->exactly(1))
                ->method('rollback')
                ->willReturn(null);
        $objectManager
                ->expects($this->exactly(0))
                ->method('flush');
        $objectManager
                ->expects($this->exactly(0))
                ->method('commit');

        // The test
        try {
            $service = new ResumeService($objectManager);
            $service->removeWithDependencies($resume);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.resume.delete", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
            $this->assertInstanceOf(\Doctrine\ORM\ORMException::class, $exc->getPrevious(), "2. The error has to be a ORMException type.");
        } catch (\Error | \Exception $exc) {
            $this->fail("3. Unexpected exception! " . $exc->getMessage());
        }
    }

    /**
     * Test the `publish` method for nominal case.
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
     * Test the `publish` method with force case.
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
     * Test the `publish` method with resume already online.
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
     * Test the `publish` method with resume incomplete.
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
     * Test the `publish` method with resume not anonymous and no user profile.
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
     * Test the `unpublish` method with an incomplete resume.
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
     * Test the `unpublish` method with an complete resume.
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

    /**
     * Test the `getDetailsForResume` method.
     *
     * @return void
     */
    public function testGetDetailsForResumeWithProfile(): void {
        // Create the mocks for the test.
        $resume = $this->createMock(Resume::class);
        $resume->expects($this->any())
                ->method('getId')
                ->willReturn(1);
        $resume->expects($this->once())
                ->method('getAuthor')
                ->willReturn(new User());
        $resume->expects($this->exactly(2))
                ->method("getAnonymous")
                ->willReturn(false);

        $resumeRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeRepositoryMock->expects($this->once())
                ->method("findWithTranslationAndAuthor")
                ->willReturn($resume);

        $profileRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ProfileRepository::class);
        $profileRepositoryMock->expects($this->once())
                ->method('getWithAvatarForUser')
                ->willReturn(new \Com\Nairus\ResumeBundle\Entity\Profile());

        $educationRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\EducationRepository::class);
        $educationRepositoryMock->expects($this->once())
                ->method("findOrderedForResumeId")
                ->willReturn(new \Com\Nairus\ResumeBundle\Collection\EducationCollection([new Education()]));

        $experienceRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ExperienceRepository::class);
        $experienceRepositoryMock->expects($this->once())
                ->method("findOrderedForResumeId")
                ->willReturn(new \Com\Nairus\ResumeBundle\Collection\ExperienceCollection([new Experience()]));

        $resumeSkillRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository::class);
        $resumeSkillRepositoryMock->expects($this->once())
                ->method("findOrderedByRank")
                ->willReturn(new \Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection([new ResumeSkill()]));

        $map = [
            [Resume::class, $resumeRepositoryMock],
            [Profile::class, $profileRepositoryMock],
            [Education::class, $educationRepositoryMock],
            [Experience::class, $experienceRepositoryMock],
            [ResumeSkill::class, $resumeSkillRepositoryMock]
        ];
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager
                ->expects($this->exactly(5))
                ->method('getRepository')
                ->willReturnMap($map);

        // launch the test.
        $service = new ResumeService($objectManager);
        $dto = $service->getDetailsForResumeId(1, "fr");

        $this->assertNotNull($dto, "1. The DTO has not to be null");
        $this->assertNotNull($dto->getProfile(), "2. The profile has not to be null.");
        $this->assertNotEmpty($dto->getEducations(), "3. The education's collection has not to be empty.");
        $this->assertNotEmpty($dto->getExperiences(), "4. The experience's collection has not to be empty.");
        $this->assertNotEmpty($dto->getResumeSkills(), "5. The resumeSkill's collection has not to be empty.");
        $this->assertFalse($dto->isAnonymous(), "6. The dto has not to be anonymous.");
    }

    /**
     * Test the `getDetailsForResume` method.
     *
     * @return void
     */
    public function testGetDetailsForResumeIdWithNoProfile(): void {
        // Create the mocks for the test.
        $resume = $this->createMock(Resume::class);
        $resume->expects($this->any())
                ->method('getId')
                ->willReturn(1);
        $resume->expects($this->exactly(0))
                ->method('getAuthor')
                ->willReturn(new User());
        $resume->expects($this->any())
                ->method("getAnonymous")
                ->willReturn(true);

        $resumeRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeRepositoryMock->expects($this->once())
                ->method("findWithTranslationAndAuthor")
                ->willReturn($resume);

        $profileRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ProfileRepository::class);
        $profileRepositoryMock->expects($this->exactly(0))
                ->method('getWithAvatarForUser');

        $educationRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\EducationRepository::class);
        $educationRepositoryMock->expects($this->once())
                ->method("findOrderedForResumeId");

        $experienceRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ExperienceRepository::class);
        $experienceRepositoryMock->expects($this->once())
                ->method("findOrderedForResumeId");

        $resumeSkillRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository::class);
        $resumeSkillRepositoryMock->expects($this->once())
                ->method("findOrderedByRank");

        $map = [
            [Resume::class, $resumeRepositoryMock],
            [Profile::class, $profileRepositoryMock],
            [Education::class, $educationRepositoryMock],
            [Experience::class, $experienceRepositoryMock],
            [ResumeSkill::class, $resumeSkillRepositoryMock]
        ];
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager
                ->expects($this->exactly(5))
                ->method('getRepository')
                ->willReturnMap($map);

        // launch the test.
        $service = new ResumeService($objectManager);
        $dto = $service->getDetailsForResumeId(1, "fr");

        $this->assertNotNull($dto, "1. The DTO has not to be null");
        $this->assertNull($dto->getProfile(), "2. The profile has to be null.");
        $this->assertTrue($dto->isAnonymous(), "3. The dto has to be anonymous.");
    }

    /**
     * Test the `getDetailsForResumeId` method.
     *
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     *
     * @return void
     */
    public function testGetDetailsForResumeIdWithNoResume(): void {
        // Create the mocks for the test.
        $resume = $this->createMock(Resume::class);
        $resume->expects($this->any())
                ->method('getId')
                ->willReturn(1);
        $resume->expects($this->exactly(0))
                ->method('getAuthor')
                ->willReturn(new User());
        $resume->expects($this->any())
                ->method("getAnonymous")
                ->willReturn(true);

        $resumeRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeRepositoryMock->expects($this->once())
                ->method("findWithTranslationAndAuthor")
                ->willReturn(null);

        $profileRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ProfileRepository::class);
        $profileRepositoryMock->expects($this->exactly(0))
                ->method('getWithAvatarForUser');

        $educationRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\EducationRepository::class);
        $educationRepositoryMock->expects($this->exactly(0))
                ->method("findOrderedForResumeId");

        $experienceRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ExperienceRepository::class);
        $experienceRepositoryMock->expects($this->exactly(0))
                ->method("findOrderedForResumeId");

        $resumeSkillRepositoryMock = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository::class);
        $resumeSkillRepositoryMock->expects($this->exactly(0))
                ->method("findOrderedByRank");

        $map = [
            [Resume::class, $resumeRepositoryMock],
            [Profile::class, $profileRepositoryMock],
            [Education::class, $educationRepositoryMock],
            [Experience::class, $experienceRepositoryMock],
            [ResumeSkill::class, $resumeSkillRepositoryMock]
        ];
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager
                ->expects($this->exactly(5))
                ->method('getRepository')
                ->willReturnMap($map);

        // launch the test.
        $service = new ResumeService($objectManager);
        $service->getDetailsForResumeId(1, "fr");
    }

}
