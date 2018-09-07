<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;

/**
 * Test of SkillService.
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
     * Test the implemenation of SkillServiceInterface from IoC.
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

}
