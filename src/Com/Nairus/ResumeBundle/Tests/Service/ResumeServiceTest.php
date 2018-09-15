<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\ResumeBundle\Enums\ExceptionCodeEnums;
use Com\Nairus\ResumeBundle\Exception\ResumeListException;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

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
     * Class to load online resume's fixtures.
     *
     * @var LoadResumeOnline
     */
    private $loadResumeOnline;

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
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new ResumeService(static::$em);
        $this->loadResumeOnline = new LoadResumeOnline();
        $this->loadResumeOnline->load(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $this->loadResumeOnline->remove(static::$em);
    }

    /**
     * Test the implementations of the service.
     *
     * @return void
     */
    public function testImplementations(): void {
        $this->assertInstanceOf(ResumeServiceInterface::class, $this->object, "1. Le service doit être de type [ResumeServiceInterface].");
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
            /* @var $resumePaginator \Doctrine\ORM\Tools\Pagination\Paginator */
            $resumePaginator = $this->object->findAllOnlineForPage(1, 2);

            $this->assertSame(2, $resumePaginator->count(), "1. Il doit y avoir 2 CV en ligne au total.");
            $this->assertCount(2, $resumePaginator->getQuery()->getResult(), "2. Il doit y avoir 2 CV sur la première page.");
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levée:" . $exc->getMessage());
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
            $this->object->findAllOnlineForPage(0, 50);
        } catch (ResumeListException $exc) {
            $this->assertSame(0, $exc->getPage(), "1. Le numéro de page doit être correcte.");
            $this->assertSame(ExceptionCodeEnums::WRONG_PAGE, $exc->getCode(), "2. Le code de l'exception doit être correcte.");
        } catch (\Exception | \Error $exc) {
            $this->fail("Exception/Erreur inatendue: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Service\ResumeService::findAllOnlineForPage
     */
    public function testFindAllOnlineForPageWithPageNotExists(): void {
        try {
            $this->object->findAllOnlineForPage(2, 50);
        } catch (ResumeListException $exc) {
            $this->assertSame(2, $exc->getPage(), "1. Le numéro de page doit être correcte.");
            $this->assertSame(ExceptionCodeEnums::PAGE_NOT_FOUND, $exc->getCode(), "2. Le code de l'exception doit être correcte.");
        } catch (\Exception | \Error $exc) {
            $this->fail("Exception/Erreur inatendue: " . $exc->getMessage());
        }
    }

    /**
     * Test the instanciation by the ioc.
     */
    public function testLoadWithIoc(): void {
        try {
            $resumeService = static::$container->get("ns_resume.resume_service");
            $this->assertInstanceOf(ResumeServiceInterface::class, $resumeService, "1. Le service doit être du bon type.");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception non attendue: " . $exc->getMessage());
        }
    }

}
