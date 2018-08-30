<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;

class NewsControllerTest extends BaseWebTestCase {

    /**
     * Test the access control on "/news" routes.
     */
    public function testAccessControlList(): void {
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2 Unexpected HTTP status code for GET /news/ with admin login");

        $client->request("GET", "/news", [], [], [
            "PHP_AUTH_USER" => "nairus",
            "PHP_AUTH_PW" => '789']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3 Unexpected HTTP status code for GET /news/ with nairus login");
    }

    public function testIndexActionNotFoundException(): void {
        $client = $this->getClient();

        // Test first page with no news.
        $crawler = $client->request("GET", "/news", [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 200 status code expected");
        $noNewsMessage = "Il n'y a pas de news pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !";
        $body = $crawler->filter(".container-fluid > em")->text();
        $this->assertContains($noNewsMessage, $body, "1.2 The [no-news] message has to be displayed");

        // Non existing second page 404 error.
        $client->request("GET", "/news/2", [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "2.1 404 status code expected");
    }

    /**
     * Test 400 bad request use case.
     */
    public function testIndexActionBadRequestException(): void {
        $client = $this->getClient();
        $client->request("GET", "/news/0", [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "1. 400 status code expected");
    }

    /**
     * Test complete scenario, add, edit, delete and read News.
     */
    public function testCompleteScenario(): void {
        // Create a new client to browse the application
        $client = $this->getClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/news/', [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /news/");
        $crawler = $client->click($crawler->selectLink('Ajouter une news')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => 'Test titre',
            'com_nairus_corebundle_newscontent[description]' => 'Test description',
            'com_nairus_corebundle_newscontent[link]' => 'http://www.google.com',
            'com_nairus_corebundle_newscontent[locale]' => "fr",
            'com_nairus_corebundle_newscontent[news][published]' => false,
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('h5:contains("Test titre")')->count(), 'Missing element td:contains("Test titre")');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "The [add] flash message is missing.");

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Modifier')->link());

        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => 'Foo'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('h5:contains("Foo")')->count(), 'Missing element [value="Foo"]');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "The [edit] flash message is missing.");

        // Delete the entity
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "The [delete] flash message is missing.");

        // Select in database to make sure there no news left.
        $container = static::$kernel->getContainer();
        $em = $container->get("doctrine")->getManager();
        /* @var $newsRepository \Com\Nairus\CoreBundle\Repository\NewsRepository */
        $newsRepository = $em->getRepository(NSCoreBundle::NAME . ":News");
        $news = $newsRepository->findAll();
        $this->assertCount(0, $news, "No news has to remain in database.");
    }

    /**
     * Test translation and publish actions.
     */
    public function testTranslationAndPublishAction(): void {
        // Create a new client to browse the application
        $client = $this->getClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/news/', [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);

        // Click on the add button
        $crawler = $client->click($crawler->selectLink('Ajouter une news')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => 'Test titre',
            'com_nairus_corebundle_newscontent[description]' => 'Test description',
            'com_nairus_corebundle_newscontent[link]' => 'http://www.google.com',
            'com_nairus_corebundle_newscontent[locale]' => "fr",
            'com_nairus_corebundle_newscontent[news][published]' => false,
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Go on the list and check missing translation in the list.
        $crawler = $client->click($crawler->selectLink('Retour à la liste')->link());
        $this->assertGreaterThan(0, $crawler->filter('.missing-translations > div > div > a > .ns-flag-en')->count(), 'The missing translation link is missing');

        // Click on the the translation button.
        $crawler = $client->click($crawler->selectLink('en')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => 'Test title',
            'com_nairus_corebundle_newscontent[description]' => 'Test description',
            'com_nairus_corebundle_newscontent[link]' => 'http://www.google.com',
            'com_nairus_corebundle_newscontent[locale]' => "en"
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "The [edit] flash message is missing.");

        // Go on the list and check if there is no missing translation in the list.
        $crawler = $client->click($crawler->selectLink('Retour à la liste')->link());
        $this->assertEquals(0, $crawler->filter('.missing-translations > div')->children()->count(), 'No missing translation has to be missing');

        // Click on publish button
        $form = $crawler->selectButton('Publier')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Select in database.
        $container = static::$kernel->getContainer();
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $container->get("doctrine")->getManager();
        /* @var $newsRepository \Com\Nairus\CoreBundle\Repository\NewsRepository */
        $newsRepository = $em->getRepository(NSCoreBundle::NAME . ":News");
        $newsList = $newsRepository->findBy(["published" => true]);
        $this->assertCount(1, $newsList, "One news has to be online.");
        /* @var $news \Com\Nairus\CoreBundle\Entity\News */
        $news = $newsList[0];
        $this->assertCount(2, $news->getContents(), "The news has to have 2 contents.");

        // Clean database.
        $em->remove($news);
        $em->flush();
    }

    /**
     * Test form validation.
     *
     * @return void
     */
    public function testFormValidation(): void {
        // Create a new client to browse the application
        $client = $this->getClient();

        // Connect the the admin news homepage.
        $crawler = $client->request('GET', '/news/', [], [], [
            "PHP_AUTH_USER" => "admin",
            "PHP_AUTH_PW" => '456']);

        // Click on the add button
        $crawler = $client->click($crawler->selectLink('Ajouter une news')->link());
        $this->assertCount(0, $crawler->filter(".invalid-feedback"), "1. The form has to show no error.");

        // Fill in the form
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => ' ',
            'com_nairus_corebundle_newscontent[description]' => ' ',
            'com_nairus_corebundle_newscontent[link]' => ' ',
            'com_nairus_corebundle_newscontent[locale]' => "fr",
            'com_nairus_corebundle_newscontent[news][published]' => false,
        ));

        // Submit the form
        $crawler = $client->submit($form);

        // Verify if there are some errors
        $this->assertCount(3, $crawler->filter(".is-invalid"), "2.1 The form has to show 3 inputs in error.");
        $this->assertCount(3, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 3 errors messages.");
    }

}
