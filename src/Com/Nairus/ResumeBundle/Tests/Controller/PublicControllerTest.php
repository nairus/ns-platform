<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;

/**
 * Public controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PublicControllerTest extends BaseWebTestCase {

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
    }

}
