<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;

class HomepageControllerTest extends BaseWebTestCase {

    public function testIndex() {
        $crawler = $this->getClient()->request("GET", "/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Accueil - Travaux de Nairus", $headTitle, "2.1 The title has to be well set");
        $this->assertEquals(3, $crawler->filter("#navbar ul li")->count(), "2.2 The navbar has to display 3 public items");
    }

    public function testIndexEn() {
        $crawler = $this->getClient()->request("GET", "/en/");
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [200] code.");

        $headTitle = $crawler->filter("html > head > title")->text();
        $this->assertEquals("Home - Nairus Works", $headTitle, "2. The title has to be well set");
    }

    public function testContact() {
        $client = $this->getClient();
        $client->request("GET", "/contact");
        $this->assertEquals(302, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [302] code.");

        $crawler = $client->followRedirect();
        $uriRedirect = $client->getRequest()->getUri();
        $this->assertEquals("http://localhost/", $uriRedirect, "2.1 The redirect uri has to be correct");
        $this->assertContains(
                "La page de contact n'est pas encore disponible, merci de revenir plus tard.",
                $crawler->filter(".message-container .alert-info")->text(),
                "2.2 The redirect message has to be correct.");
    }

    public function testContactEn() {
        $client = $this->getClient();
        $client->request("GET", "/en/contact");
        $this->assertEquals(302, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [302] code.");

        $crawler = $client->followRedirect();
        $uriRedirect = $client->getRequest()->getUri();
        $this->assertEquals("http://localhost/en/", $uriRedirect, "2.1 The redirect uri has to be correct");
        $this->assertContains(
                "Contact page is not yet available, please come back later.",
                $crawler->filter(".message-container .alert-info")->text(),
                "2.2 The redirect message has to be correct.");
    }

    public function testNotFoundPage() {
        $this->getClient()->request("GET", "/not-exist");
        $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [404] code.");
    }

    public function testNotFoundPageEn() {
        $this->getClient()->request("GET", "/en/not-exist");
        $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(), "1. The response has to return a [404] code.");
    }

}
