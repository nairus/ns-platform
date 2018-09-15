<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

/**
 * Test of Skill service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillServiceTest extends AbstractKernelTestCase {

    /**
     * Instance of SkillService.
     *
     * @var SkillService
     */
    private $object;

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
        $loadSkill = new LoadSkill();
        $loadSkill->remove(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->remove(static::$em);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->object = new SkillService(static::$em);
    }

    /**
     * The the implementation of SkillServiceInterface.
     *
     * @return void
     */
    public function testImplementations(): void {
        $this->assertInstanceOf(SkillServiceInterface::class, $this->object, "1. The service is not of type [SkillServiceInterface].");
    }

    /**
     * Test the implementation of SkillServiceInterface from IoC.
     *
     * @return void
     */
    public function testLoadWithIoc(): void {
        try {
            $skillService = static::$container->get("ns_resume.skill_service");
            $this->assertInstanceOf(SkillServiceInterface::class, $skillService, "1. The service is not of type [SkillServiceInterface].");
            $this->assertInstanceOf(SkillService::class, $skillService, "2. The service is not of type [SkillService].");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception not expected: " . $exc->getMessage());
        }
    }

    /**
     * Test <code>findAllForPage</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\SkillService::findAllForPage
     *
     * @return void
     */
    public function testFindAllForPage(): void {
        /* @var $skillPaginatorDto SkillPaginatorDto */
        $skillPaginatorDto = $this->object->findAllForPage(1, 2);
        $this->assertNotNull($skillPaginatorDto, "1. The DTO should not be null.");
        $this->assertCount(2, $skillPaginatorDto->getEntities(), "2. The Dto should contain 2 entities.");
        $this->assertEquals(1, $skillPaginatorDto->getPages(), "3. The number of pages is not the one expected.");
        $this->assertEquals(1, $skillPaginatorDto->getCurrentPage(), "4. The current page is not the one expected.");
    }

    /**
     * Test <code>findAllForPage</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\SkillService::findAllForPage
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\PaginatorException
     *
     * @return void
     */
    public function testFindAllForPageWithWithWrongPage(): void {
        $this->object->findAllForPage(0, 1);
    }

    /**
     * Test <code>findAllForPage</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\SkillService::findAllForPage
     *
     * @return void
     */
    public function testFindAllForPageWithPageNotExists(): void {
        /* @var $skillPaginatorDto SkillPaginatorDto */
        $skillPaginatorDto = $this->object->findAllForPage(2, 2);
        $this->assertNotNull($skillPaginatorDto, "1. The DTO should not be null.");
        $this->assertCount(0, $skillPaginatorDto->getEntities(), "2. The Dto should contain no entity.");
        $this->assertEquals(1, $skillPaginatorDto->getPages(), "3. The number of pages is not the one expected.");
        $this->assertEquals(2, $skillPaginatorDto->getCurrentPage(), "4. The current page is not the one expected.");
    }

    /**
     * Test <code>removeSkill</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Service\SkillService::removeSkill
     * @covers Com\Nairus\ResumeBundle\Repository\SkillRepository::remove
     *
     * @return void
     */
    public function testRemoveSkillCaseNominal(): void {
        $skill = new Skill();
        $skill->setTitle("Test OK");
        static::$em->persist($skill);
        static::$em->flush($skill);

        $this->assertFalse($skill->isNew(), "1. The entity hasn't to be new.");
        $id = $skill->getId();

        $this->object->removeSkill($skill);
        $skillRemoved = static::$em->find(NSResumeBundle::NAME . ":Skill", $id);
        $this->assertNull($skillRemoved, "2. The entity has to be deleted.");
    }

    /**
     * Test remove for a skill linked to a resume.
     *
     * @return void
     */
    public function testRemoveSkillWithLinkedResume(): void {
        try {
            // Create new skill
            $skill = new Skill();
            $skill->setTitle("Test OK");

            // Link it to a resume
            $resume = static::$em->find(NSResumeBundle::NAME . ":Resume", 1);
            $skillLevel = static::$em->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];
            $resumeSkill = new ResumeSkill();
            $resumeSkill->setRank(1)
                    ->setResume($resume)
                    ->setSkill($skill)
                    ->setSkillLevel($skillLevel);

            static::$em->persist($skill);
            static::$em->persist($resumeSkill);
            static::$em->flush();

            // Try to remove the skill
            $this->object->removeSkill($skill);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.skill.delete.resume-linked", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Exception $exc) {
            $this->fail("2. Unexpected exception: " . $exc->getMessage());
        } finally {
            $this->cleanDatas();
        }
    }

    /**
     * Test doctrine errors management.
     *
     * @return void
     */
    public function testRemoveSkillWithADoctrineError(): void {
        // Create mocks for the test.
        // First mock the entity to return.
        $skill = $this->createMock(Skill::class);
        $skill->expects($this->any())
                ->method("getId")
                ->willReturn(1);

        // Now, mock the repositories
        $resumeSkillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeSkillRepository->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        $skillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\SkillRepository::class);
        $skillRepository->expects($this->once())
                ->method('remove')
                ->willThrowException(new \Doctrine\ORM\ORMException("An Error occured!"));

        // Last, mock the EntityManager to return the mock of the repositories
        $datas = [
            [NSResumeBundle::NAME . ":Skill", $skillRepository],
            [NSResumeBundle::NAME . ":ResumeSkill", $resumeSkillRepository]
        ];
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $objectManager->expects($this->any())
                ->method('getRepository')
                ->willReturnMap($datas);

        try {
            $service = new SkillService($objectManager);
            $service->removeSkill($skill);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.skill.delete.failed", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Error | \Exception $exc) {
            $this->fail("2. Unexpected exception! " . $exc->getMessage());
        }
    }

    /**
     * Test <code>removeSkill</code> with unknown error.
     *
     * @return void
     */
    public function testRemoveSkillWithAnUnknownError(): void {
        // Create mocks for the test.
        // First mock the entity to return.
        $skill = $this->createMock(Skill::class);
        $skill->expects($this->any())
                ->method("getId")
                ->willReturn(1);

        // Now, mock the repositories
        $resumeSkillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\ResumeRepository::class);
        $resumeSkillRepository->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        $skillRepository = $this->createMock(\Com\Nairus\ResumeBundle\Repository\SkillRepository::class);
        $skillRepository->expects($this->once())
                ->method('remove')
                ->willThrowException(new \Exception("An Error occured!"));

        // Last, mock the EntityManager to return the mock of the repositories
        $objectManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $datas = [
            [NSResumeBundle::NAME . ":Skill", $skillRepository],
            [NSResumeBundle::NAME . ":ResumeSkill", $resumeSkillRepository]
        ];
        $objectManager->expects($this->any())
                ->method('getRepository')
                ->willReturnMap($datas);

        try {
            $service = new SkillService($objectManager);
            $service->removeSkill($skill);
        } catch (FunctionalException $exc) {
            $this->assertEquals("flashes.error.unknown", $exc->getTranslationKey(), "1. The translation key expected is not ok.");
        } catch (\Error | \Exception $exc) {
            $this->fail("2. Unexpected exception! " . $exc->getMessage());
        }
    }

    /**
     * Clean datas after test.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function cleanDatas(): void {
        // Reset the entity manager to prevent "Doctrine\ORM\ORMException".
        static::$container
                ->get("doctrine")
                ->resetManager();

        static::$em = static::$container
                ->get("doctrine")
                ->getManager();

        $skillClassMetadata = static::$em->getClassMetadata(Skill::class);
        $resumeSkillClassMetadata = static::$em->getClassMetadata(ResumeSkill::class);
        $connection = static::$em->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('PRAGMA foreign_keys = OFF');
            $q1 = $databasePlatform->getTruncateTableSql($resumeSkillClassMetadata->getTableName());
            $connection->executeUpdate($q1);
            $q2 = $databasePlatform->getTruncateTableSql($skillClassMetadata->getTableName());
            $connection->executeUpdate($q2);
            $connection->query('PRAGMA foreign_keys = ON');
            $connection->commit();
        } catch (\Exception $exc) {
            $connection->rollBack();
            throw $exc;
        }
    }

}
