<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;

/**
 * News controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsControllerTest extends AbstractUserWebTestCase {

    /**
     * Test the access control on "/admin/news" routes for user.
     *
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::indexAction
     *
     * @return void
     */
    public function testAccessControlListWithUser(): void {
        $client = $this->getClient();
        $client->request("GET", "/admin/news");
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "1. 302 redirection to /login expected");

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. Unexpected HTTP status code for GET /admin/news/ with user login");

        // Fill in the form and submit it with bad credential
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'user',
            '_password' => 'userpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "3. Unexpected HTTP status code for GET /admin/news for user login");
    }

    /**
     * Test the access control on "/admin/news" routes for author.
     *
     * @return void
     */
    public function testAccessControlListWithAuthor(): void {
        $client = $this->getClient();
        $client->request("GET", "/admin/news");
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "1. 302 redirection to /login expected");

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. Unexpected HTTP status code for GET /admin/news/ with author login");

        // Fill in the form and submit it with bad credential
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'author',
            '_password' => 'authorpass',
        ));

        $client->submit($form);
        $client->followRedirect();
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "3. Unexpected HTTP status code for GET /admin/news for author login");
    }

    /**
     * Test the access control on "/admin/news" routes for moderator.
     *
     * @return void
     */
    public function testAccessControlListWithModerator(): void {
        $client = $this->getClient();
        $client->request("GET", "/admin/news");
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "1. 302 redirection to /login expected");

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. Unexpected HTTP status code for GET /admin/news/ with moderator login");

        // Fill in the form and submit it with bad credential
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'moderator',
            '_password' => 'moderatorpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "3. Unexpected HTTP status code for GET /admin/news for moderator login");
    }

    /**
     * Test the access control on "/admin/news" routes for admin.
     *
     * @return void
     */
    public function testAccessControlListWithAdmin(): void {
        $client = $this->getClient();
        $client->request("GET", "/admin/news");
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "1. 302 redirection to /login expected");

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. Unexpected HTTP status code for GET /login/ with admin login");

        // Fill in the form and submit it with good credential
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'admin',
            '_password' => 'adminpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3. Unexpected HTTP status code for GET /admin/news/ with admin login");
        $this->assertContains("Liste des news", $crawler->filter("html > head > title")->text(), "4.1 The page title expected is not ok.");
        $this->assertContains("Liste des news", $crawler->filter("h1")->text(), "4.2 The h1 tag expected is not ok.");
    }

    /**
     * Test access control list with sadmin credential.
     *
     * @return void
     */
    public function testAccessControlListWithSAdmin(): void {
        $client = $this->getClient();
        $client->request("GET", "/en/admin/news");
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "1. 302 redirection to /en/login expected");

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. Unexpected HTTP status code for GET /en/login/ with sadmin login");

        // Fill in the form and submit it with good credential
        $form = $crawler->selectButton('Log in')->form(array(
            '_username' => 'sadmin',
            '_password' => 'sadminpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3. Unexpected HTTP status code for GET /en/admin/news/ with sadmin login");
        $this->assertContains("News list", $crawler->filter("html > head > title")->text(), "4.1 The page title expected is not ok.");
        $this->assertContains("News list", $crawler->filter("h1")->text(), "4.2 The h1 tag expected is not ok.");
    }

    /**
     * Test not found exception in index action.
     *
     * @return void
     */
    public function testIndexActionNotFoundException(): void {
        // Login with good credential.
        $this->logInAdmin();

        // Test first page with no news.
        $client = $this->getClient();
        $crawler = $client->request("GET", "/admin/news");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 200 status code expected");
        $noNewsMessage = "Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !";
        $body = $crawler->filter(".container-fluid > em")->text();
        $this->assertContains($noNewsMessage, $body, "1.2 The [no-news] message has to be displayed");

        // Non existing second page 404 error.
        $client->request("GET", "/admin/news/2");
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "2.1 404 status code expected");
    }

    /**
     * Test display list with no news in english.
     *
     * @return void
     */
    public function testIndexActionNoNewsInEn(): void {
        // Login with good credential.
        $this->logInAdmin();

        // Test first page with no news.
        $client = $this->getClient();
        $crawler = $client->request("GET", "/en/admin/news");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 200 status code expected");
        $noNewsMessage = "There is no item for now! Please add one clicking on the button above!";
        $body = $crawler->filter(".container-fluid > em")->text();
        $this->assertContains($noNewsMessage, $body, "1.2 The [no-news] message has to be displayed");
    }

    /**
     * Test 400 bad request use case.
     *
     * @return void
     */
    public function testIndexActionBadRequestException(): void {
        // Login with good credential.
        $this->logInAdmin();

        $client = $this->getClient();
        $client->request("GET", "/admin/news/0");
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "1. 400 status code expected");
    }

    /**
     * Test complete scenario, add, edit, delete and read News.
     *
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::newAction
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::editAction
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::showAction
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::deleteAction
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        // Login with good credential.
        $crawler = $this->logInAdmin();

        // Create a new client to browse the application
        $client = $this->getClient();

        // Create a new entry in the database
        $crawler = $client->click($crawler->selectLink("Gestion des News")->link());

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 Unexpected HTTP status code for GET /admin/news/");
        $this->assertEquals("/admin/news", $client->getRequest()->getRequestUri(), "1.2 Unexpected Request URI");
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
        $this->assertRegExp("~^/admin/news/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "1.3 Unexpected Request URI");

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('h5:contains("Test titre")')->count(), '2.1 Missing element td:contains("Test titre")');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.2 The [add] flash message is missing.");
        $this->assertContains("News ajoutée avec succès !", $crawler->filter('.message-container')->text(), "2.3 The [add] flash message is not ok.");

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Modifier')->link());
        $this->assertRegExp("~^/admin/news/[0-9]+/edit~", $client->getRequest()->getRequestUri(), "2.4 The request uri expected is not ok");
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_corebundle_newscontent[title]' => 'Foo'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('h5:contains("Foo")')->count(), '3.1 Missing element [value="Foo"]');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.2 The [edit] flash message is missing.");
        $this->assertRegExp("~News n°[0-9]+ modifiée avec succès !~", $crawler->filter('.message-container')->text(), "3.3 The [edit] flash message is not ok.");

        // Delete the entity
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "The [delete] flash message is missing.");
        $this->assertRegExp("~News n°[0-9]+ supprimée avec succès !~", $crawler->filter('.message-container')->text(), "3.3 The [delete] flash message is not ok.");

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
     *
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::translationAction
     * @covers Com\Nairus\CoreBundle\Controller\NewsController::publishAction
     *
     * @return void
     */
    public function testTranslationAndPublishAction(): void {
        // Login with good credential.
        $this->logInAdmin();

        // Create a new client to browse the application
        $client = $this->getClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/news/');

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
        $this->assertGreaterThan(0, $crawler->filter('.missing-translations > div > div > a > .ns-flag-en')->count(), '1.1 The missing translation link is missing');

        // Click on the the translation button.
        $crawler = $client->click($crawler->selectLink('en')->link());
        $this->assertRegExp("~^/admin/news/[0-9]+/translation/en~", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok");

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
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.1 The [add] flash message is missing.");
        $this->assertRegExp('~Contenu "en" ajouté avec succès pour la news "[0-9]+" !~', $crawler->filter('.message-container')->text(), "2.2 The [add] flash message is not ok.");

        // Go on the list and check if there is no missing translation in the list.
        $crawler = $client->click($crawler->selectLink('Retour à la liste')->link());
        $this->assertEquals(0, $crawler->filter('.missing-translations > div')->children()->count(), '2.3 No missing translation has to be missing');

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
        $this->assertCount(1, $newsList, "2.4 One news has to be online.");
        /* @var $news \Com\Nairus\CoreBundle\Entity\News */
        $news = $newsList[0];
        $this->assertCount(2, $news->getContents(), "2.5 The news has to have 2 contents.");

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
        // Login with good credential.
        $this->logInAdmin();

        // Create a new client to browse the application
        $client = $this->getClient();

        // Connect the the admin news homepage.
        $crawler = $client->request('GET', '/admin/news/');

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
