<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Com\Nairus\CoreBundle\Entity as NSEntity;

/**
 * Test of ContactMessage controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageControllerTest extends AbstractUserWebTestCase {

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        parent::setUp();

        // load datas for testing
        $this->loadDatas($this->getEntityManager(), [new \Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadContactMessage()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Truncate the datas table.
        $this->cleanDatas($this->getClient()->getContainer(), [NSEntity\ContactMessage::class, NSEntity\BlacklistedIp::class]);

        parent::tearDown();
    }

    /**
     * Test access control list with user.
     *
     * @return void
     */
    public function testAccessWithUser(): void {
        $this->logInUser();
        $this->getClient()->request("GET", "/sadmin/contact");

        $this->assertEquals(403, $this->getClient()->getResponse()->getStatusCode(), "The status code expected is not ok.");
    }

    /**
     * Test access control list with admin.
     *
     * @return void
     */
    public function testAccessWithAdmin(): void {
        $this->logInAdmin();
        $this->getClient()->request("GET", "/sadmin/contact");

        $this->assertEquals(403, $this->getClient()->getResponse()->getStatusCode(), "The status code expected is not ok.");
    }

    /**
     * Test access control list with moderator.
     *
     * @return void
     */
    public function testAccessWithModerator(): void {
        $this->logInModerator();
        $this->getClient()->request("GET", "/sadmin/contact");

        $this->assertEquals(403, $this->getClient()->getResponse()->getStatusCode(), "The status code expected is not ok.");
    }

    /**
     * Test complete scenario in fr.
     */
    public function testCompleteScenarioFr(): void {
        $crawler = $this->logInSuperAdmin();
        $client = $this->getClient();
        $crawler = $client->click($crawler->filter("#navbar")->selectLink("Gestion des contacts")->link());

        // 1. list all messages
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertContains("Gestion des contacts - Travaux", $crawler->filter("html > head > title")->text(), "1.3 The page title is not ok");
        $this->assertEquals("Liste des contacts", $crawler->filter("h1")->text(), "1.4 The page h1 is not ok");
        $this->assertNotNull($crawler->filter("#contacts-container > table"), "1.5 The message's list is missing");
        $theadCols = $crawler->filter("#contacts-container > table > thead > tr > th");
        $this->assertEquals(6, $theadCols->count(), "1.6 Six cols are expected");
        $this->assertEquals("Nom / Prénom", $theadCols->eq(1)->text(), "1.7 The col title is not ok");
        $this->assertEquals("Tél. / Email", $theadCols->eq(2)->text(), "1.8 The col title is not ok");
        $this->assertEquals("Ip", $theadCols->eq(3)->text(), "1.9 The col title is not ok");
        $this->assertEquals("Date de demande", $theadCols->eq(4)->text(), "1.10 The col title is not ok");
        $this->assertEquals("Gestions", $theadCols->eq(5)->text(), "1.11 The col title is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertEquals(2, $tbodyRow->count(), "1.12 Two rows are expected");
        $this->assertContains('<i class="far fa-comment-dots"></i>', $tbodyRow->eq(0)->children()->eq(1)->html(), "1.13 The message icon is missing");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(0)->children()->eq(3)->html(), "1.14 The blacklisted icon is missing");
        $this->assertCount(2, $tbodyRow->eq(0)->children()->eq(5)->filter("ul > li"), "1.15 Two actions button are expected");
        $this->assertContains('<i class="far fa-comment-dots"></i>', $tbodyRow->eq(1)->children()->eq(1)->html(), "1.16 The message icon is missing");
        $this->assertNotContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(1)->children()->eq(3)->html(), "1.17 The blacklisted icon is not expected");
        $this->assertCount(3, $tbodyRow->eq(1)->children()->eq(5)->filter("ul > li"), "1.18 Three actions button is expected");
        $this->assertNotNull($crawler->filter("#message-contact-pager"), "1.19 The pagination container is expected");

        // 2. go to page 2 and delete with list button
        $crawler = $client->click($crawler->filter("#message-contact-pager > nav > ul")->selectLink("2")->link());
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact/2", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Gestion des contacts - Page 2 - Travaux", $crawler->filter("html > head > title")->text(), "2.3 The page title is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertEquals(1, $tbodyRow->count(), "2.4 One row is expected");
        $this->assertCount(2, $tbodyRow->eq(0)->children()->eq(5)->filter("ul > li"), "2.5 Two actions button are expected");
        $deleteForm = $tbodyRow->eq(0)->children()->eq(5)->filter("ul")->selectButton("Supprimer")->form();
        $client->submit($deleteForm);
        $crawler = $client->followRedirect();

        // 3. return to the list (one page with no pagination)
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "3.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "3.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "3.4 One success message is expected");
        $this->assertRegExp("~Le message n°\"[0-9]+\" a été supprimé avec succès !~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "3.5 The success message is not ok");
        $this->assertEquals(0, $crawler->filter("#message-contact-pager")->count(), "3.6 The pager container should not be displayed.");

        // 4. blacklist the second item with list page buttons
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $blacklistForm = $tbodyRow->eq(1)->children()->eq(5)->filter("ul")->selectButton("Ip sur liste noire")->form();
        $client->submit($blacklistForm);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "4.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "4.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "4.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "4.4 One success message is expected");
        $this->assertRegExp("~L'ip \"[0-9\.]+\" a été blacklistée avec succès !~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "4.5 The success message is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(1)->children()->eq(3)->html(), "4.6 The blacklisted icon is not expected");
        $this->assertCount(2, $tbodyRow->eq(1)->children()->eq(5)->filter("ul > li"), "4.7 Two actions button are expected");

        // 5. go to the show page and delete with the show page buttons
        $crawler = $client->click($tbodyRow->eq(1)->children()->eq(5)->filter("ul > li")->selectLink("Voir les détails")->link());
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "5.1 The status code expected is not ok.");
        $this->assertRegExp("~/sadmin/contact/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "5.2 The request uri expected is not ok.");
        $this->assertRegExp("~Détail du message n°[0-9]+~", $crawler->filter("html > head > title")->text(), "5.3 The page title is not ok");
        $this->assertRegExp("~^Détail du message n°[0-9]+$~", $crawler->filter("h1")->text(), "5.3 The page h1 is not ok");
        $content = $crawler->filter("#admin-container .card .card-body");
        $this->assertContains("Son Goku", $content->text(), "5.4 The contact name is missing");
        $this->assertContains("Bonjour le monde", $content->text(), "5.5 The contact message is missing");
        $this->assertContains("goku@dbsuper.com", $content->text(), "5.6 The contact email is missing");
        $this->assertRegExp("~127.0.0.[0-9]+~", $content->text(), "5.7 The contact ip is missing");
        $actionButtons = $crawler->filter("#admin-container .card .actions")->children();
        $this->count(2, $actionButtons, "5.8 Two action buttons are expected.");
        $this->assertContains("Retour à la liste", $actionButtons->eq(0)->text(), "5.9 The label of the first button is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionButtons->eq(0)->html(), "5.10 The icon of the first button is not ok.");
        $this->assertContains("Supprimer", $actionButtons->eq(1)->text(), "5.11 The label of the first button is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionButtons->eq(1)->html(), "5.12 The icon of the first button is not ok.");

        // delete the message
        $client->submit($actionButtons->eq(1)->selectButton("Supprimer")->form());
        $crawler = $client->followRedirect();

        // verify the result
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "5.13 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "5.14 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "5.15 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "5.16 One success message is expected");
        $this->assertRegExp("~Le message n°\"[0-9]+\" a été supprimé avec succès !~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "5.17 The success message is not ok");
        $this->assertEquals(1, $crawler->filter("#contacts-container > table > tbody > tr")->count(), "5.18 One row is expected");

        // 6. delete the last message and verify the no-item message is displayed.
        $client->submit($crawler->selectButton("Supprimer")->form());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "6.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "6.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "6.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "6.4 One success message is expected");
        $this->assertRegExp("~Le message n°\"[0-9]+\" a été supprimé avec succès !~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "6.5 The success message is not ok");
        $this->assertCount(0, $crawler->filter("#contacts-container > table"), "6.6 No row is expected");
        $this->assertContains("Il n'y a aucune donnée pour le moment !", $crawler->filter("#contacts-container")->text(), "6.7 The no-item label is missing");
    }

    /**
     * Test the blacklist form from show page.
     *
     * @return void
     */
    public function testBlacklistFromShowPage(): void {
        // go to the show page of the second item.
        $crawler = $this->logInSuperAdmin();
        $client = $this->getClient();
        $crawler = $client->click($crawler->filter("#navbar")->selectLink("Gestion des contacts")->link());
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $crawler = $client->click($tbodyRow->eq(1)->children()->eq(5)->filter("ul > li")->selectLink("Voir les détails")->link());

        // Verify if the blacklist button is displayed and submit the form.
        $actionButtons = $crawler->filter("#admin-container .card .actions")->children();
        $this->count(3, $actionButtons, "1.1. Two action buttons are expected.");
        $this->assertContains("Ip sur liste noire", $actionButtons->eq(2)->text(), "1.2. The label of the third button is not ok.");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $actionButtons->eq(2)->html(), "1.3. The icon of the third button is not ok.");

        // blacklist the message ip and verify the result
        $client->submit($actionButtons->eq(2)->selectButton('Ip sur liste noire')->form());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "2.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "2.4 One success message is expected");
        $this->assertRegExp("~L'ip \"[0-9\.]+\" a été blacklistée avec succès !~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "2.5 The success message is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(1)->children()->eq(3)->html(), "2.6 The blacklisted icon is not expected");
        $this->assertCount(2, $tbodyRow->eq(1)->children()->eq(5)->filter("ul > li"), "2.7 Two actions button are expected");
    }

    /**
     * Test complete scenario in en.
     */
    public function testCompleteScenarioEn(): void {
        $crawler = $this->logInSuperAdmin("en");
        $client = $this->getClient();
        $crawler = $client->click($crawler->filter("#navbar")->selectLink("Manage Contacts")->link());

        // 1. list all messages
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertContains("Contacts management - Nairus", $crawler->filter("html > head > title")->text(), "1.3 The page title is not ok");
        $this->assertEquals("Contacts list", $crawler->filter("h1")->text(), "1.4 The page h1 is not ok");
        $this->assertNotNull($crawler->filter("#contacts-container > table"), "1.5 The message's list is missing");
        $theadCols = $crawler->filter("#contacts-container > table > thead > tr > th");
        $this->assertEquals(6, $theadCols->count(), "1.6 Six cols are expected");
        $this->assertEquals("Last name / First name", $theadCols->eq(1)->text(), "1.7 The col title is not ok");
        $this->assertEquals("Phone / Email", $theadCols->eq(2)->text(), "1.8 The col title is not ok");
        $this->assertEquals("Ip", $theadCols->eq(3)->text(), "1.9 The col title is not ok");
        $this->assertEquals("Request date", $theadCols->eq(4)->text(), "1.10 The col title is not ok");
        $this->assertEquals("Actions", $theadCols->eq(5)->text(), "1.11 The col title is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertEquals(2, $tbodyRow->count(), "1.12 Two rows are expected");
        $this->assertContains('<i class="far fa-comment-dots"></i>', $tbodyRow->eq(0)->children()->eq(1)->html(), "1.13 The message icon is missing");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(0)->children()->eq(3)->html(), "1.14 The blacklisted icon is missing");
        $this->assertCount(2, $tbodyRow->eq(0)->children()->eq(5)->filter("ul > li"), "1.15 Two actions button are expected");
        $this->assertContains('<i class="far fa-comment-dots"></i>', $tbodyRow->eq(1)->children()->eq(1)->html(), "1.16 The message icon is missing");
        $this->assertNotContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(1)->children()->eq(3)->html(), "1.17 The blacklisted icon is not expected");
        $this->assertCount(3, $tbodyRow->eq(1)->children()->eq(5)->filter("ul > li"), "1.18 Three actions button is expected");
        $this->assertNotNull($crawler->filter("#message-contact-pager"), "1.19 The pagination container is expected");

        // 2. go to page 2 and delete with list button
        $crawler = $client->click($crawler->filter("#message-contact-pager > nav > ul")->selectLink("2")->link());
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact/2", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Contacts management - Page 2 - Nairus", $crawler->filter("html > head > title")->text(), "2.3 The page title is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertEquals(1, $tbodyRow->count(), "2.4 One row is expected");
        $this->assertCount(2, $tbodyRow->eq(0)->children()->eq(5)->filter("ul > li"), "2.5 Two actions button are expected");
        $deleteForm = $tbodyRow->eq(0)->children()->eq(5)->filter("ul")->selectButton("Delete")->form();
        $client->submit($deleteForm);
        $crawler = $client->followRedirect();

        // 3. return to the list (one page with no pagination)
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "3.1 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "3.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "3.4 One success message is expected");
        $this->assertRegExp("~The message No.\"[0-9]+\" has been successfully deleted!~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "3.5 The success message is not ok");
        $this->assertEquals(0, $crawler->filter("#message-contact-pager")->count(), "3.6 The pager container should not be displayed.");

        // 4. blacklist the second item with list page buttons
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $blacklistForm = $tbodyRow->eq(1)->children()->eq(5)->filter("ul")->selectButton("Blacklist ip")->form();
        $client->submit($blacklistForm);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "4.1 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "4.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "4.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "4.4 One success message is expected");
        $this->assertRegExp("~The \"[0-9\.]+\" ip has been successfully blacklisted!~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "4.5 The success message is not ok");
        $tbodyRow = $crawler->filter("#contacts-container > table > tbody > tr");
        $this->assertContains('<i class="fas fa-user-slash"></i>', $tbodyRow->eq(1)->children()->eq(3)->html(), "4.6 The blacklisted icon is not expected");
        $this->assertCount(2, $tbodyRow->eq(1)->children()->eq(5)->filter("ul > li"), "4.7 Two actions button are expected");

        // 5. go to the show page and delete with the show page buttons
        $crawler = $client->click($tbodyRow->eq(1)->children()->eq(5)->filter("ul > li")->selectLink("Show details")->link());
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "5.1 The status code expected is not ok.");
        $this->assertRegExp("~/en/sadmin/contact/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "5.2 The request uri expected is not ok.");
        $this->assertRegExp("~Detail of the message No. [0-9]+~", $crawler->filter("html > head > title")->text(), "5.3 The page title is not ok");
        $this->assertRegExp("~^Detail of the message No. [0-9]+$~", $crawler->filter("h1")->text(), "5.3 The page h1 is not ok");
        $content = $crawler->filter("#admin-container .card .card-body");
        $this->assertContains("Son Goku", $content->text(), "5.4 The contact name is missing");
        $this->assertContains("Bonjour le monde", $content->text(), "5.5 The contact message is missing");
        $this->assertContains("goku@dbsuper.com", $content->text(), "5.6 The contact email is missing");
        $this->assertRegExp("~127.0.0.[0-9]+~", $content->text(), "5.7 The contact ip is missing");
        $actionButtons = $crawler->filter("#admin-container .card .actions")->children();
        $this->count(2, $actionButtons, "5.8 Two action buttons are expected.");
        $this->assertContains("Return to the list", $actionButtons->eq(0)->text(), "5.9 The label of the first button is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionButtons->eq(0)->html(), "5.10 The icon of the first button is not ok.");
        $this->assertContains("Delete", $actionButtons->eq(1)->text(), "5.11 The label of the first button is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionButtons->eq(1)->html(), "5.12 The icon of the first button is not ok.");

        // delete the message
        $client->submit($actionButtons->eq(1)->selectButton("Delete")->form());
        $crawler = $client->followRedirect();

        // verify the result
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "5.13 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "5.14 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "5.15 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "5.16 One success message is expected");
        $this->assertRegExp("~The message No.\"[0-9]+\" has been successfully deleted!~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "5.17 The success message is not ok");
        $this->assertEquals(1, $crawler->filter("#contacts-container > table > tbody > tr")->count(), "5.18 One row is expected");

        // 6. delete the last message and verify the no-item message is displayed.
        $client->submit($crawler->selectButton("Delete")->form());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "6.1 The status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "6.2 The request uri expected is not ok.");
        $this->assertNotNull($crawler->filter("#admin-container .message-container"), "6.3 The message container is missing");
        $this->assertCount(1, $crawler->filter("#admin-container .message-container .alert-success"), "6.4 One success message is expected");
        $this->assertRegExp("~The message No.\"[0-9]+\" has been successfully deleted!~", $crawler->filter("#admin-container .message-container .alert-success")->text(),
                "6.5 The success message is not ok");
        $this->assertCount(0, $crawler->filter("#contacts-container > table"), "6.6 No row is expected");
        $this->assertContains("There is no item for now!", $crawler->filter("#contacts-container")->text(), "6.7 The no-item label is missing");
    }

    /**
     * Test error management in blacklist action.
     *
     * @return void
     */
    public function testBlacklistActionErrorInFr(): void {
        // get the entity to blacklist
        /* @var $contactMessage NSEntity\ContactMessage */
        $contactMessage = $this->getEntityManager()->getRepository(NSEntity\ContactMessage::class)->findOneByIp("127.0.0.1");

        // prepare the datas to post
        $client = $this->getClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('contact_message_form');
        $parameters = [
            "form" => [
                "_token" => $csrfToken,
            ]
        ];

        // launch the test
        $this->logInSuperAdmin();
        $client->request("POST", sprintf("/sadmin/contact/%d/blacklist", $contactMessage->getId()), $parameters);
        $crawler = $client->followRedirect();

        // verify the test
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The http status code expected is not ok.");
        $this->assertEquals("/sadmin/contact", $client->getRequest()->getRequestUri(), "2. The request uri expected is not ok");
        $this->assertEquals(1, $crawler->filter(".message-container .alert-danger")->count(), "3. The error message is missing");
        $this->assertContains(sprintf("L'ip \"%s\" a déjà été blacklistée !", $contactMessage->getIp()),
                $crawler->filter(".message-container .alert-danger")->text(), "4. The error message expected is not ok.");
    }

    /**
     * Test error management in blacklist action.
     *
     * @return void
     */
    public function testBlacklistActionErrorInEn(): void {
        // get the entity to blacklist
        /* @var $contactMessage NSEntity\ContactMessage */
        $contactMessage = $this->getEntityManager()->getRepository(NSEntity\ContactMessage::class)->findOneByIp("127.0.0.1");

        // prepare the datas to post
        $client = $this->getClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('contact_message_form');
        $parameters = [
            "form" => [
                "_token" => $csrfToken,
            ]
        ];

        // launch the test
        $this->logInSuperAdmin("en");
        $client->request("POST", sprintf("/en/sadmin/contact/%d/blacklist", $contactMessage->getId()), $parameters);
        $crawler = $client->followRedirect();

        // verify the test
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The http status code expected is not ok.");
        $this->assertEquals("/en/sadmin/contact", $client->getRequest()->getRequestUri(), "2. The request uri expected is not ok");
        $this->assertEquals(1, $crawler->filter(".message-container .alert-danger")->count(), "3. The error message is missing");
        $this->assertContains(sprintf("The \"%s\" ip has been blacklisted already!", $contactMessage->getIp()),
                $crawler->filter(".message-container .alert-danger")->text(), "4. The error message expected is not ok.");
    }

}
