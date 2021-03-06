<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadNewsPublished;

/**
 * Homepage controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class HomepageControllerTest extends BaseWebTestCase {

    /**
     * Test index action.
     *
     * @covers Com\Nairus\CoreBundle\Controller\HomepageController::indexAction
     *
     * @return void
     */
    public function testIndex(): void {
        // Load datas set.
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getClient()->getContainer()->get("doctrine")->getManager();
        $loadNewsPublished = new LoadNewsPublished();
        $loadNewsPublished->load($em);

        $crawler = $this->getClient()->request("GET", "/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Accueil - Travaux de Nairus", $headTitle, "2.1 The title has to be well set");
        $this->assertEquals(3, $crawler->filter("#navbar ul li")->count(), "2.2 The navbar has to display 3 public items");

        // Test the last news bloc.
        $this->assertEquals(1, $crawler->filter(".last-news-container")->count(), "3.1 The last news container has to be in the DOM.");
        $this->assertEquals(2, $crawler->filter(".last-news-tile")->count(), "3.2 2 news tiles has to be in the DOM");

        // Remove data set for other tests.
        $loadNewsPublished->remove($em);
    }

    /**
     * Test index action with no news.
     *
     * @return void
     */
    public function testIndexWithNoNews(): void {
        $crawler = $this->getClient()->request("GET", "/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        // Test the last news bloc.
        $this->assertNotContains('<div class="container last-news-container">', $crawler->filter(".homepage")->html(), "2. The last news container has not to be in the DOM.");
    }

    /**
     * Test index action in english.
     *
     * @return void
     */
    public function testIndexEn(): void {
        // Load datas set.
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getClient()->getContainer()->get("doctrine")->getManager();
        $loadNewsPublished = new LoadNewsPublished();
        $loadNewsPublished->load($em);

        $crawler = $this->getClient()->request("GET", "/en/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Home - Nairus Works", $headTitle, "2. The title has to be well set");

        // Test the last news bloc.
        $this->assertEquals(1, $crawler->filter(".last-news-container")->count(), "3.1 The last news container has to be in the DOM.");
        $this->assertEquals(1, $crawler->filter(".last-news-tile")->count(), "3.2 Only 1 news tile has to be in the DOM");

        // Remove data set for other tests.
        $loadNewsPublished->remove($em);
    }

    /**
     * Test not found exception.
     *
     * @return void
     */
    public function testNotFoundPage(): void {
        $this->getClient()->request("GET", "/not-exist");
        $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [404] code.");
    }

    /**
     * Test not found exception in english.
     *
     * @return void
     */
    public function testNotFoundPageEn(): void {
        $this->getClient()->request("GET", "/en/not-exist");
        $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [404] code.");
    }

}
