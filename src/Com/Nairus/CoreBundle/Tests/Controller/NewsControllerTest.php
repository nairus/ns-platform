<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;

class NewsControllerTest extends BaseWebTestCase {

    /**
     * Test the access control on "/news" routes.
     */
    public function testAccessControlList() {
        // Try authentication with bad credentials.
        $client = $this->getClient();
        $client->request("GET", "/news", [], [], [
            "PHP_AUTH_USER" => "user",
            "PHP_AUTH_PW" => '123'
        ]);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. Unexpected HTTP status code for GET /news/ with user login");

        // Try authentication with good credential.
        $client->request("GET", "/news", [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2 Unexpected HTTP status code for GET /news/ with admin login");

        $client->request("GET", "/news", [], [], [
            "PHP_AUTH_USER" => "nairus",
            "PHP_AUTH_PW" => '789']);
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3 Unexpected HTTP status code for GET /news/ with nairus login");
    }

    /**
     * Test complete scenario, add, edit, delete and read News.
     */
    public function testCompleteScenario() {
        $this->markTestIncomplete("TODO");
//        // Create a new client to browse the application
//        $client = static::createClient();
//
//        // Create a new entry in the database
//        $crawler = $client->request('GET', '/news/');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /news/");
//        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
//
//        // Fill in the form and submit it
//        $form = $crawler->selectButton('Create')->form(array(
//            'com_nairus_corebundle_news[field_name]'  => 'Test',
//            // ... other fields to fill
//        ));
//
//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//        // Check data in the show view
//        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');
//
//        // Edit the entity
//        $crawler = $client->click($crawler->selectLink('Edit')->link());
//
//        $form = $crawler->selectButton('Update')->form(array(
//            'com_nairus_corebundle_news[field_name]'  => 'Foo',
//            // ... other fields to fill
//        ));
//
//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//        // Check the element contains an attribute with value equals "Foo"
//        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
//
//        // Delete the entity
//        $client->submit($crawler->selectButton('Delete')->form());
//        $crawler = $client->followRedirect();
//
//        // Check the entity has been delete on the list
//        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

}
