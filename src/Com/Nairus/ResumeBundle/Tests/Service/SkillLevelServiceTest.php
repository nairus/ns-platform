<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

/**
 * Test of SkillLevel service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelServiceTest extends AbstractKernelTestCase {

    /**
     * Instance of SkillService.
     *
     * @var SkillLevelService
     */
    private $object;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        // Load test fixtures.
        $loadSkill = new LoadSkill();
        $loadSkill->load(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->load(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass() {
        // Remove test fixtures.
        static::cleanDatasAfterTest(static::$container, [new LoadSkill()], new LoadSkillLevel());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->object = new SkillLevelService(static::$em);
    }

    /**
     * Test the implementation of SkillLevelServiceInterface.
     *
     * @return void
     */
    public function testImplementations(): void {
        $this->assertInstanceOf(SkillLevelServiceInterface::class, $this->object, "1. The service is not of type [SkillLevelServiceInterface].");
    }

    /**
     * Test the implementation of SkillLevelServiceInterface from IoC.
     *
     * @return void
     */
    public function testLoadWithIoc(): void {
        try {
            $skillLevelService = static::$container->get("ns_resume.skill_level_service");
            $this->assertInstanceOf(SkillLevelServiceInterface::class, $skillLevelService, "1. The service is not of type [SkillLevelServiceInterface].");
            $this->assertInstanceOf(SkillLevelServiceInterface::class, $skillLevelService, "2. The service is not of type [SkillLevelServiceInterface].");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception not expected: " . $exc->getMessage());
        }
    }

    /**
     * Test `removeSkillLevel` method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\SkillLevelService::removeSkillLevel
     * @covers Com\Nairus\ResumeBundle\Repository\SkillLevelRepository::remove
     *
     * @return void
     */
    public function testRemoveSkilLevelCaseNominal(): void {
        $skillLevel = new SkillLevel();
        $skillLevel
                ->setCurrentLocale("fr")
                ->setTitle("Test OK");
        static::$em->persist($skillLevel);
        static::$em->flush($skillLevel);

        $this->assertFalse($skillLevel->isNew(), "1. The entity hasn't to be new.");
        $id = $skillLevel->getId();

        $this->object->removeSkillLevel($skillLevel);
        $skillRemoved = static::$em->find(NSResumeBundle::NAME . ":SkillLevel", $id);
        $this->assertNull($skillRemoved, "2. The entity has to be deleted.");
    }

    /**
     * Test remove for a skill level linked to a resume.
     *
     * @return void
     */
    public function testRemoveSkillLevelWithLinkedResume(): void {
        try {
            // Create new skill level
            $skillLevel = new SkillLevel();
            $skillLevel
                    ->setCurrentLocale("fr")
                    ->setTitle("Test OK");

            // Link it to a resume
            $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
            $skill = static::$em->getRepository(NSResumeBundle::NAME . ":Skill")->findAll()[0];
            $resumeSkill = new ResumeSkill();
            $resumeSkill->setRank(1)
                    ->setResume($resume)
                    ->setSkill($skill)
                    ->setSkillLevel($skillLevel);

            static::$em->persist($skillLevel);
            static::$em->persist($resumeSkill);
            static::$em->flush();

            // Try to remove the skill
            $this->object->removeSkillLevel($skillLevel);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.skill-level.delete.resume-linked", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Exception $exc) {
            $this->fail("2. Unexpected exception: " . $exc->getMessage());
        } finally {
            $this->cleanDatas(static::$container, [Skill::class, ResumeSkill::class]);
        }
    }

    /**
     * Test doctrine errors management.
     *
     * @return void
     */
    public function testRemoveSkillLevelWithADoctrineError(): void {
        // Create mocks for the test.
        // First mock the entity to return.
        $skillLevel = $this->createMock(SkillLevel::class);
        $skillLevel->expects($this->any())
                ->method("getId")
                ->willReturn(1);

        // Now, mock the repositories
        $resumeSkillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeSkillRepository->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        $skillLevelRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\SkillLevelRepository::class);
        $skillLevelRepository->expects($this->once())
                ->method('remove')
                ->willThrowException(new \Doctrine\ORM\ORMException("An Error occured!"));

        // Last, mock the EntityManager to return the mock of the repositories
        $datas = [
            [NSResumeBundle::NAME . ":SkillLevel", $skillLevelRepository],
            [NSResumeBundle::NAME . ":ResumeSkill", $resumeSkillRepository]
        ];
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager->expects($this->any())
                ->method('getRepository')
                ->willReturnMap($datas);

        try {
            $service = new SkillLevelService($objectManager);
            $service->removeSkillLevel($skillLevel);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.skill-level.delete.failed", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Error | \Exception $exc) {
            $this->fail("2. Unexpected exception! " . $exc->getMessage());
        }
    }

    /**
     * Test `removeSkillLEvel` with unknown error.
     *
     * @return void
     */
    public function testRemoveSkillLevelWithAnUnknownError(): void {
        // Create mocks for the test.
        // First mock the entity to return.
        $skillLevel = $this->createMock(SkillLevel::class);
        $skillLevel->expects($this->any())
                ->method("getId")
                ->willReturn(1);

        // Now, mock the repositories
        $resumeSkillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeSkillRepository->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        $skillLevelRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\SkillLevelRepository::class);
        $skillLevelRepository->expects($this->once())
                ->method('remove')
                ->willThrowException(new \Exception("An Error occured!"));

        // Last, mock the EntityManager to return the mock of the repositories
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $datas = [
            [NSResumeBundle::NAME . ":SkillLevel", $skillLevelRepository],
            [NSResumeBundle::NAME . ":ResumeSkill", $resumeSkillRepository]
        ];
        $objectManager->expects($this->any())
                ->method('getRepository')
                ->willReturnMap($datas);

        // The test.
        try {
            $service = new SkillLevelService($objectManager);
            $service->removeSkillLevel($skillLevel);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.unknown", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Error | \Exception $exc) {
            $this->fail("2. Unexpected exception! " . $exc->getMessage());
        }
    }

}
