<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

/**
 * Public controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PublicControllerIndexTest extends BaseWebTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * Init the HTTP test client for each tests.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        // Prepare datas test set.
        $this->loadDatas($this->getEntityManager(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // clean datas set
        $this->cleanDatas($this->getClient()->getContainer(), [new LoadResumeOnline(), Skill::class, new LoadSkillLevel()]);

        parent::tearDown();
    }

    /**
     * Test index action.
     *
     * @return void
     */
    public function testIndex(): void {
        $crawler = $this->getClient()->request("GET", "/resume/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Plateforme des CV - Travaux de Nairus", $headTitle, "2. The title has to be well set");
        $this->assertEquals("Plateforme des CV", $crawler->filter("body > main > div.container > h1")->text(), "3. The h1 has to be well set");
        $this->assertEquals("Performante et moderne", $crawler->filter("body > main > div.container > h2")->text(), "4. The h2 has to be well set");

        // get the resume tiles
        $resumeTiles = $crawler->filter('.resume-tile');
        $this->assertCount(2, $resumeTiles, "5. Two tiles are expected.");
        $this->assertContains("Compétences", $resumeTiles->eq(0)->text(), "6.1 The tile has to contains skills label");
        $this->assertContains("Expériences", $resumeTiles->eq(0)->text(), "6.2 The tile has to contains experiences label");
        $this->assertContains("Diplômes", $resumeTiles->eq(0)->text(), "6.3 The tile has to contains educations label");

        // Test with no resume online
        $this->cleanDatas($this->getClient()->getContainer(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);
        $crawler = $this->getClient()->request("GET", "/resume/");
        $this->assertEquals(0, $crawler->filter('.resume-tile')->count(), "7. No tile is expected.");
        $this->assertContains("Aucun CV en ligne trouvé", $crawler->filter(".no-resume")->text(), "8. The no resume message is expected.");
    }

    /**
     * Test index action in english.
     *
     * @return void
     */
    public function testIndexEn(): void {
        $crawler = $this->getClient()->request("GET", "/en/resume/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Resumes platform - Nairus Works", $headTitle, "2. The title has to be well set");
        $this->assertEquals("Resumes platform", $crawler->filter("body > main > div.container > h1")->text(), "3. The h1 has to be well set");
        $this->assertEquals("Efficient and modern", $crawler->filter("body > main > div.container > h2")->text(), "4. The h2 has to be well set");

        // get the resume tiles
        $resumeTiles = $crawler->filter('.resume-tile');
        $this->assertCount(1, $resumeTiles, "5. One tile is expected.");
        $this->assertContains("Skills", $resumeTiles->eq(0)->text(), "6.1 The tile has to contains skills label");
        $this->assertContains("Experiences", $resumeTiles->eq(0)->text(), "6.2 The tile has to contains experiences label");
        $this->assertContains("Educations", $resumeTiles->eq(0)->text(), "6.3 The tile has to contains educations label");

        // Test with no resume online
        $this->cleanDatas($this->getClient()->getContainer(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);
        $crawler = $this->getClient()->request("GET", "/en/resume/");
        $this->assertEquals(0, $crawler->filter('.resume-tile')->count(), "7. No tile is expected.");
        $this->assertContains("No online resume found", $crawler->filter(".no-resume")->text(), "8. The no resume message is expected.");
    }

    /**
     * Test if the controller handle wrong page exception from the resume service.
     *
     * @return void
     */
    public function testIndexBadParameterException(): void {
        $this->getClient()->request("GET", "/resume/0");
        $this->assertEquals(400, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [400] code.");
    }

    /**
     * Test if the controller handle page not found exception from the resume service.
     *
     * @return void
     */
    public function testIndexNotFoundException(): void {
        $this->getClient()->request("GET", "/resume/2");
        $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [404] code.");
    }

}
