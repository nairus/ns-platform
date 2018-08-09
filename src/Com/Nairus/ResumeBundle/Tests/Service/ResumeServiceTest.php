<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\ResumeBundle\Enums\ExceptionCodeEnums;
use Com\Nairus\ResumeBundle\Exception\ResumeListException;
use Com\Nairus\ResumeBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-08-28 at 07:52:09.
 */
class ResumeServiceTest extends AbstractKernelTestCase {

    /**
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
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ResumeService(static::$em);
        $this->loadResumeOnline = new LoadResumeOnline();
        $this->loadResumeOnline->load(static::$em);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->loadResumeOnline->remove(static::$em);
    }

    public function testImplementations()
    {
        $this->assertInstanceOf(ResumeServiceInterface::class, $this->object, "1. Le service doit être de type [ResumeServiceInterface].");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Service\ResumeServiceImpl::findAllOnlineForPage
     */
    public function testFindAllOnlineForPage()
    {
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
     * @covers Com\Nairus\ResumeBundle\Service\ResumeServiceImpl::findAllOnlineForPage
     */
    public function testFindAllOnlineForPageWithWrongPage()
    {
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
     * @covers Com\Nairus\ResumeBundle\Service\ResumeServiceImpl::findAllOnlineForPage
     */
    public function testFindAllOnlineForPageWithPageNotExists()
    {
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
    public function testLoadWithIoc()
    {
        try {
            $resumeService = static::$container->get("ns_resume.resume_service");
            $this->assertInstanceOf(ResumeServiceInterface::class, $resumeService, "1. Le service doit être du bon type.");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception non attendue: " . $exc->getMessage());
        }
    }

}
