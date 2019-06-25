<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Com\Nairus\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Resume controller functional tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeControllerTest extends AbstractUserWebTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        parent::setUp();

        // Prepare datas test set.
        $this->loadDatas($this->getEntityManager(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Force clean datas set.
        $this->removeAllDatasSet();

        parent::tearDown();
    }

    /**
     * Test index action with user credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::indexAction
     *
     * @return void
     */
    public function testIndexActionWithAuthorCredentials(): void {
        $crawler = $this->logInAuthor();
        $client = $this->getClient();

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
        $this->assertEquals("/restricted/resume/", $client->getRequest()->getRequestUri(), "3. The request uri expected is not ok.");
        $this->assertCount(1, $crawler->filter("#resume-container > .row")->children(), "4. The page has to contain one element.");
    }

    /**
     * Test index action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::indexAction
     *
     * @return void
     */
    public function testIndexActionWithAdminCredentials(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
        $this->assertEquals("/restricted/resume/", $client->getRequest()->getRequestUri(), "3. The request uri expected is not ok.");
        $this->assertContains("Il n'y a aucune donnée pour le moment !",
                $crawler->filter("#resume-container")->text(), "4. The page has to contain no element.");
    }

    /**
     * Test edit action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::editAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testEditActionWithAdminCredentials(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go the the resume edit form of an other user's resume.
        $client->request("GET", "/restricted/resume/1/edit");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
    }

    /**
     * Test show action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::showAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testShowActionWithAdminCredentials(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go the the resume show page of an other user's resume.
        $client->request("GET", "/restricted/resume/1/show");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
    }

    /**
     * Test publish action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::publishAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testPublishActionWithAdminCredentials(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go the the resume delete action of an other user's resume.
        $client->request("PATCH", "/restricted/resume/1/publish");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
    }

    /**
     * Test unpublishAction action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::unpublishAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testUnpublishActionWithAdminCredentials(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go the the resume show page of an other user's resume.
        $client->request("PATCH", "/restricted/resume/1/unpublish");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
    }

    /**
     * Test delete action with Admin credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\ResumeController::deleteAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testDeleteActionWithAdminCredentials(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go the the resume delete action of an other user's resume.
        $client->request("DELETE", "/restricted/resume/1/delete");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
    }

    /**
     * Test complete scenario in fr.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        // Login as user
        $crawler = $this->logInUser();
        $client = $this->getClient();

        // Case 1: go to index page
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
        $this->assertEquals("/restricted/resume/", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Liste de mes CV", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
        $this->assertEquals("Liste de mes CV", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#resume-add-new")->html(), "1.6 The add button has not the picto expected.");
        $this->assertContains("Ajouter un nouveau CV", $crawler->filter("#resume-add-new")->text(), "1.7 The add button has not the content expected.");
        $this->assertContains("Il n'y a aucune donnée pour le moment !",
                $crawler->filter("#resume-container")->text(), "1.8 The page has to contain the no-item message.");

        // Case 2: add a new resume
        $crawler = $client->click($crawler->selectLink("Ajouter un nouveau CV")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/restricted/resume/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertEquals(2, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "2.3 Two actions buttons are expected.");
        $actionsElements = $crawler->filter("#admin-container > .jumbotron > form > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements, "2.4 The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $actionsElements, "2.5 The return label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements, "2.6 The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $actionsElements, "2.7 The save label expected is not ok");
        $this->assertContains("Ajout d'un CV", $crawler->filter("html > head > title")->text(), "2.8 The title page expected is not ok.");
        $this->assertEquals("Ajout d'un CV", $crawler->filter("h1")->text(), "2.9 The h1 title expected is not ok.");
        $formElements = $crawler->filter("#com_nairus_resumebundle_resume > .form-group");
        $this->assertCount(2, $formElements, "2.10 The form has to contain four two elements");
        $this->assertEquals("div", $formElements->getNode(0)->nodeName, "2.11 The first element node name expected is not ok.");
        $this->assertContains("Anonyme ?", $formElements->getNode(0)->textContent, "2.12 The first element label expected is not ok.");
        $this->assertEquals("fieldset", $formElements->getNode(1)->nodeName, "2.13 The second element node name expected is not ok.");
        $this->assertContains("Traductions", $formElements->getNode(1)->textContent, "2.14 The second element label expected is not ok.");
        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => true,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre FR',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 3: show the resume
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '3.1 The request uri is not OK');
        $this->assertRegExp("~Détail du CV n° [0-9+]~", $crawler->filter('html > head > title')->text(), '3.2 The page title excepted is not OK');
        $this->assertRegExp("~Détail du CV n° [0-9+]~", $crawler->filter('h1')->text(), '3.3 The h1 label excepted is not OK');
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("Titre FR", $adminContainer, '3.4 The resume title in FR is missing');
        $this->assertContains('<img width="50" src="/bundles/nscore/img/flags/fr.png">', $adminContainer, '3.5 The FR flag is missing or incorrect');
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '3.6 The label status is missing or incorrect');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '3.7 The status picto is missing or incorrect');
        $this->assertContains('<i class="fas fa-user-secret"></i>', $adminContainer, '3.8 The anonymous picto is missing');
        $this->assertEquals(4, $crawler->filter("#admin-container .actions")->count(), '3.9 Four actions button are expected');
        $actionsElementElements = $crawler->filter("#admin-container .actions");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElementElements->eq(0)->html(), "3.10 The return flag is missing");
        $this->assertContains('Retour à la liste', $actionsElementElements->eq(0)->text(), "3.11 The return title is invalid");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsElementElements->eq(1)->html(), "3.12 The edit flag is missing");
        $this->assertContains('Modifier', $actionsElementElements->eq(1)->text(), "3.13 The edit title is invalid");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsElementElements->eq(2)->html(), "3.14 The return flag is missing");
        $this->assertContains('Supprimer', $actionsElementElements->eq(2)->text(), "3.15 The delete title is invalid");
        $this->assertContains('<i class="fas fa-globe"></i>', $actionsElementElements->eq(3)->html(), "3.16 The publish flag is missing");
        $this->assertContains('Publier', $actionsElementElements->eq(3)->text(), "3.17 The publish title is invalid");
        $this->assertNotNull($crawler->filter("#resume-detailed-contents"), "3.18 The detailed contents block is missing");
        $this->assertContains("Informations détaillées", $crawler->filter("#resume-detailed-contents h3")->text(), "3.19 The h3 title is incorrect");
        $this->assertEquals(3, $crawler->filter("#resume-detailed-contents ul li")->count(), "3.20 Three tabs are expected");
        $this->assertContains("Mes compétences", $crawler->filter("#resume-detailed-contents ul")->text(), "3.21 My skills title is missing");
        $this->assertContains("Mes formations", $crawler->filter("#resume-detailed-contents ul")->text(), "3.22 My educations title is missing");
        $this->assertContains("Mes expériences", $crawler->filter("#resume-detailed-contents ul")->text(), "3.23 My experiences title is missing");
        $this->assertEquals(3, $crawler->filter("#datas-contents")->children()->count(), "3.24 Tree contents bloc are expected");
        $this->assertContains("Il n'y a aucune donnée pour le moment !",
                $crawler->filter("#skills")->text(), "3.25 The no-item label is expected");
        $this->assertContains("Ajouter une compétence", $crawler->filter("#skills")->text(), "3.26 The add skill button label is expected");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#skills")->html(), "3.27 The add skill button picto is missing");
        $this->assertContains("Il n'y a aucune donnée pour le moment !",
                $crawler->filter("#educations")->text(), "3.28 The no-item label is expected");
        $this->assertContains("Ajouter une formation", $crawler->filter("#educations")->text(), "3.29 The add education button label is expected");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#educations")->html(), "3.30 The add education button picto is missing");
        $this->assertContains("Il n'y a aucune donnée pour le moment !",
                $crawler->filter("#experiences")->text(), "3.31 The no-item label is expected");
        $this->assertContains("Ajouter une expérience", $crawler->filter("#experiences")->text(), "3.32 The add experience button label is expected");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#experiences")->html(), "3.33 The add experience button picto is missing");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.34 The [add] flash message is missing.");
        $this->assertContains("CV ajouté avec succès !", $crawler->filter('.message-container')->text(), "3.35 The [add] flash message is not ok.");

        // Case 4: edit the resume
        $crawler = $client->click($crawler->selectLink("Modifier")->link());
        $this->assertRegExp("~^/restricted/resume/[0-9]+/edit~", $client->getRequest()->getRequestUri(), '4.1 The request uri is not OK');
        $this->assertRegExp("~Modification du CV n° [0-9+]~", $crawler->filter('html > head > title')->text(), '4.2 The page title excepted is not OK');
        $this->assertRegExp("~Modification du CV n° [0-9+]~", $crawler->filter('h1')->text(), '4.3 The h1 label excepted is not OK');
        $this->assertEquals(3, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "4.4 Three actions buttons are expected.");
        $actionsElements = $crawler->filter("#admin-container > .jumbotron > form > .actions")->children();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "4.5 The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $actionsElements->eq(0)->text(), "4.6 The return label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(2)->html(), "4.7 The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $actionsElements->eq(2)->text(), "4.8 The save label expected is not ok");
        $this->assertContains('<i class="far fa-eye"></i>', $actionsElements->eq(1)->html(), "4.9 The show picto expected is not in the actions div");
        $this->assertContains('Voir les détails', $actionsElements->eq(1)->text(), "4.10 The show label expected is not ok");

        $formElements = $crawler->filter("#com_nairus_resumebundle_resume > .form-group");
        $this->assertCount(2, $formElements, "4.11 The form has to contain four two elements");
        $this->assertEquals("div", $formElements->getNode(0)->nodeName, "4.12 The first element node name expected is not ok.");
        $this->assertContains("Anonyme ?", $formElements->getNode(0)->textContent, "4.13 The first element label expected is not ok.");
        $this->assertContains("checked", $crawler->filter("#com_nairus_resumebundle_resume_anonymous")->parents()->html(), "4.14 The checkbox has to be checked");
        $this->assertEquals("fieldset", $formElements->getNode(1)->nodeName, "4.15 The second element node name expected is not ok.");
        $this->assertContains("Traductions", $formElements->getNode(1)->textContent, "4.16 The second element label expected is not ok.");
        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => false,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre 2 FR',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 5: return to the show page
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '5.1 The request uri is not OK');
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "5.2 The [edit] flash message is missing.");
        $this->assertRegExp("~CV n°[0-9]+ modifié avec succès !~", $crawler->filter('.message-container')->text(), "5.3 The [edit] flash message is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("Titre 2 FR", $adminContainer, '5.4 The resume title in FR has to be modified');
        $this->assertNotContains('<i class="fas fa-user-secret"></i>', $adminContainer, '5.5 The anonymous picto has to be missing');

        // Case 6: delete the resume
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertEquals("/restricted/resume/", $client->getRequest()->getRequestUri(), "6.1 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "6.2 The [delete] flash message is missing.");
        $this->assertRegExp("~CV n°[0-9]+ supprimé avec succès !~", $crawler->filter('.message-container')->text(), "6.3 The [delete] flash message is not ok.");
        $this->assertNotContains("Titre 2 FR", $crawler->filter("#resume-container")->text(), "6.4 The resume title should not appear on the page.");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        // Login as user
        $crawler = $this->logInUser("en");
        $client = $this->getClient();

        // Case 1: go to index page
        $crawler = $client->click($crawler->selectLink("My Resumes")->link());
        $this->assertEquals("/en/restricted/resume/", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertContains("My resumes list", $crawler->filter("html > head > title")->text(), "1.2 The title page expected is not ok.");
        $this->assertEquals("My resumes list", $crawler->filter("h1")->text(), "1.3 The h1 title expected is not ok.");
        $this->assertContains("Add a new resume", $crawler->filter("#resume-add-new")->text(), "1.4 The add button has not the content expected.");
        $this->assertContains("There is no item for now!",
                $crawler->filter("#resume-container")->text(), "1.5 The page has to contain the no-item message.");

        // Case 2: add a new resume
        $crawler = $client->click($crawler->selectLink("Add a new resume")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/en/restricted/resume/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Add a resume", $crawler->filter("html > head > title")->text(), "2.3 The title page expected is not ok.");
        $this->assertEquals("Add a resume", $crawler->filter("h1")->text(), "2.4 The h1 title expected is not ok.");
        $formElements = $crawler->filter("#com_nairus_resumebundle_resume > .form-group");
        $this->assertContains("Anonymous?", $formElements->getNode(0)->textContent, "2.5 The first element label expected is not ok.");
        $this->assertContains("Translations", $formElements->getNode(1)->textContent, "2.6 The second element label expected is not ok.");
        $form = $crawler->selectButton('Save')->form([
            'com_nairus_resumebundle_resume[anonymous]' => true,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre FR',
            'com_nairus_resumebundle_resume[translations][en][title]' => 'Title EN',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 3: show the resume
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '3.1 The request uri is not OK');
        $this->assertRegExp("~Detail of the resume No [0-9+]~", $crawler->filter('html > head > title')->text(), '3.2 The page title excepted is not OK');
        $this->assertRegExp("~Detail of the resume No [0-9+]~", $crawler->filter('h1')->text(), '3.3 The h1 label excepted is not OK');
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("Title EN", $adminContainer, '3.4 The resume title in EN is missing');
        $this->assertContains('<img width="50" src="/bundles/nscore/img/flags/en.png">', $adminContainer, '3.5 EN flag is missing or incorrect');
        $this->assertContains('Uncomplete Offline', $adminContainer, '3.6 The label status is missing or incorrect');
        $actionsElementElements = $crawler->filter("#admin-container .actions");
        $this->assertContains('Return to the list', $actionsElementElements->eq(0)->text(), "3.7 The return title is invalid");
        $this->assertContains('Edit', $actionsElementElements->eq(1)->text(), "3.8 The edit title is invalid");
        $this->assertContains('Delete', $actionsElementElements->eq(2)->text(), "3.9 The delete title is invalid");
        $this->assertContains('Publish', $actionsElementElements->eq(3)->text(), "3.10 The publish title is invalid");
        $this->assertNotNull($crawler->filter("#resume-detailed-contents"), "3.11 The detailed contents block is missing");
        $this->assertContains("Detailed informations", $crawler->filter("#resume-detailed-contents h3")->text(), "3.12 The h3 title is incorrect");
        $this->assertContains("My Skills", $crawler->filter("#resume-detailed-contents ul")->text(), "3.13 My skills title is missing");
        $this->assertContains("My Educations", $crawler->filter("#resume-detailed-contents ul")->text(), "3.14 My educations title is missing");
        $this->assertContains("My Experiences", $crawler->filter("#resume-detailed-contents ul")->text(), "3.15 My experiences title is missing");
        $this->assertContains("There is no item for now!",
                $crawler->filter("#skills")->text(), "3.16 The no-item label is expected");
        $this->assertContains("Add a skill", $crawler->filter("#skills")->text(), "3.17 The add skill button label is expected");
        $this->assertContains("There is no item for now!",
                $crawler->filter("#educations")->text(), "3.18 The no-item label is expected");
        $this->assertContains("Add an education", $crawler->filter("#educations")->text(), "3.19 The add education button label is expected");
        $this->assertContains("There is no item for now!",
                $crawler->filter("#experiences")->text(), "3.20 The no-item label is expected");
        $this->assertContains("Add an experience", $crawler->filter("#experiences")->text(), "3.21 The add experience button label is expected");
        $this->assertContains("Resume added successfully!", $crawler->filter('.message-container')->text(), "3.22 The [add] flash message is not ok.");

        // Case 4: edit the resume
        $crawler = $client->click($crawler->selectLink("Edit")->link());
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/edit~", $client->getRequest()->getRequestUri(), '4.1 The request uri is not OK');
        $this->assertRegExp("~Modification of the resume No [0-9+]~", $crawler->filter('html > head > title')->text(), '4.2 The page title excepted is not OK');
        $this->assertRegExp("~Modification of the resume No [0-9+]~", $crawler->filter('h1')->text(), '4.3 The h1 label excepted is not OK');
        $actionsElements = $crawler->filter("#admin-container > .jumbotron > form > .actions")->children();
        $this->assertContains('Return to the list', $actionsElements->eq(0)->text(), "4.4 The return label expected is not ok");
        $this->assertContains('Save', $actionsElements->eq(2)->text(), "4.5 The save label expected is not ok");
        $this->assertContains('Show details', $actionsElements->eq(1)->text(), "4.6 The show label expected is not ok");

        $formElements = $crawler->filter("#com_nairus_resumebundle_resume > .form-group");
        $this->assertContains("Anonymous?", $formElements->getNode(0)->textContent, "4.7 The first element label expected is not ok.");
        $this->assertContains("Translations", $formElements->getNode(1)->textContent, "4.8 The second element label expected is not ok.");
        $form = $crawler->selectButton('Save')->form([
            'com_nairus_resumebundle_resume[anonymous]' => false,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre FR',
            'com_nairus_resumebundle_resume[translations][en][title]' => 'Title 2 EN',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 5: return to the show page
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '5.1 The request uri is not OK');
        $this->assertRegExp("~Resume No. [0-9]+ modified successfully!~", $crawler->filter('.message-container')->text(), "5.2 The [edit] flash message is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("Title 2 EN", $adminContainer, '5.3 The resume title in EN has to be modified');
        $this->assertNotContains('<i class="fas fa-user-secret"></i>', $adminContainer, '5.4 The anonymous picto has to be missing');

        // Case 6: delete the resume
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        $this->assertEquals("/en/restricted/resume/", $client->getRequest()->getRequestUri(), "6.1 The request uri expected is not ok.");
        $this->assertRegExp("~Resume No. [0-9]+ deleted successfully!~", $crawler->filter('.message-container')->text(), "6.2 The [delete] flash message is not ok.");
        $this->assertNotContains("Title 2 EN", $crawler->filter("#resume-container")->text(), "6.3 The resume title should not appear on the page.");
    }

    /**
     * Test delete from index action.
     *
     * @return void
     */
    public function testDeleteFromIndexAction(): void {
        $crawler = $this->logInModerator();
        $client = $this->getClient();

        // Go to the index page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $this->assertCount(3, $resumeCards, "1.1 Three resumes are expected on the page.");
        $this->assertEquals(4, $resumeCards->eq(0)->filter(".actions")->count(), "1.2 Four actions buttons are expected.");
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains('<i class="far fa-eye"></i>', $actionsButtons->eq(0)->html(), "1.3 The see details picto is missing");
        $this->assertContains("Voir les détails", $actionsButtons->eq(0)->text(), "1.3 The see details label is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsButtons->eq(1)->html(), "1.4 The edit picto is missing");
        $this->assertContains("Modifier", $actionsButtons->eq(1)->text(), "1.5 The edit label is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsButtons->eq(2)->html(), "1.6 The delete picto is missing");
        $this->assertContains("Supprimer", $actionsButtons->eq(2)->text(), "1.7 The delete label is not ok");
        $this->assertContains('<i class="fas fa-low-vision"></i>', $actionsButtons->eq(3)->html(), "1.8 The unpublish picto is missing");
        $this->assertContains("Dépublier", $actionsButtons->eq(3)->text(), "1.9 The unpublish label is not ok");

        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertCount(2, $crawler->filter("#resume-container > div")->children(), "2.1 Two resumes are expected on the page.");
        $this->assertRegExp("~CV n°[0-9]+ supprimé avec succès !~", $crawler->filter('.message-container')->text(), "2.2 The [delete] flash message is not ok.");
    }

    /**
     * Test the new form validation.
     *
     * @return void
     */
    public function testValidateNewForm(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go to the new page.
        $crawler = $client->request(Request::METHOD_GET, "/restricted/resume/new");

        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[translations][fr][title]' => ' ',
        ]);
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertEquals("/restricted/resume/new", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");

        // Verify if there are some errors
        $this->assertCount(1, $crawler->filter(".is-invalid"), "2.1 The form has to show 1 input in error.");
        $this->assertCount(1, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 1 error message.");
    }

    /**
     * Test the validation of edit form.
     *
     * @return void
     */
    public function testValidateEditForm(): void {
        $crawler = $this->logInModerator();
        $client = $this->getClient();

        // Go to the show page.
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());
        $crawler = $client->click($crawler->selectLink("Modifier")->link());

        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[translations][fr][title]' => ' ',
        ]);
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resume/[0-9]+/edit~", $client->getRequest()->getRequestUri(), '1.2 The request uri is not OK');

        // Verify if there are some errors
        $this->assertCount(1, $crawler->filter(".is-invalid"), "2.1 The form has to show 1 input in error.");
        $this->assertCount(1, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 1 error message.");
    }

    /**
     * Test `publishAction` and `unpublishAction` method.
     *
     * @covers Com\Nairus\ResumeBundle\Controller\ResumeController::unpublishAction
     * @covers Com\Nairus\ResumeBundle\Controller\ResumeController::publishAction
     *
     * @return void
     */
    public function testPublishUnpublishAction(): void {
        // Login as moderator.
        $this->logInModerator();
        $client = $this->getClient();

        // Go to the index page.
        $client->request(Request::METHOD_GET, "/restricted/resume");
        $crawler = $client->followRedirect();

        // Click on the unpublish button
        $client->submit($crawler->selectButton('Dépublier')->form());
        $crawler = $client->followRedirect();

        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains('<i class="fas fa-globe"></i>', $actionsButtons->eq(3)->html(), "1.1 The publish picto is missing");
        $this->assertContains("Publier", $actionsButtons->eq(3)->text(), "1.2 The publish label is not ok");
        $this->assertRegExp("~CV n°[0-9]+ dépublié avec succès !~", $crawler->filter('.message-container')->text(), "1.3 The [unpublish] flash message is not ok.");
        $this->assertContains('Complet Hors ligne', $resumeCards->eq(0)->text(), '1.4 The label status is missing or incorrect');
        $this->assertContains('<i class="fas fa-thermometer-half"></i>', $resumeCards->eq(0)->html(), '1.5 The status picto is missing or incorrect');

        // Click on the publish button
        $client->submit($crawler->selectButton('Publier')->form());
        $crawler = $client->followRedirect();

        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains("Dépublier", $actionsButtons->eq(3)->text(), "2.1 The unpublish label is not ok");
        $this->assertRegExp("~CV n°[0-9]+ publié avec succès !~", $crawler->filter('.message-container')->text(), "2.2 The [publish] flash message is not ok.");
        $this->assertContains('En ligne', $resumeCards->eq(0)->text(), '2.3 The label status is missing or incorrect');
        $this->assertContains('<i class="fas fa-thermometer-full"></i>', $resumeCards->eq(0)->html(), '2.4 The status picto is missing or incorrect');

        // Test EN (for translations).
        $client->request(Request::METHOD_GET, "/en/restricted/resume");
        $crawler = $client->followRedirect();

        // Click on the unpublish button
        $client->submit($crawler->selectButton('Unpublish')->form());
        $crawler = $client->followRedirect();

        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains("Publish", $actionsButtons->eq(3)->text(), "3.1 The publish label is not ok");
        $this->assertRegExp("~Resume No. [0-9]+ has been unpublished successfully!~", $crawler->filter('.message-container')->text(), "3.2 The [unpublish] flash message is not ok.");
        $this->assertContains('Complete Offline', $resumeCards->eq(0)->text(), '3.3 The label status is missing or incorrect');

        // Click on the publish button
        $client->submit($crawler->selectButton('Publish')->form());
        $crawler = $client->followRedirect();

        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains("Unpublish", $actionsButtons->eq(3)->text(), "4.1 The unpublish label is not ok");
        $this->assertRegExp("~Resume No. [0-9]+ has been published successfully!~", $crawler->filter('.message-container')->text(), "4.2 The [publish] flash message is not ok.");
        $this->assertContains('Online', $resumeCards->eq(0)->text(), '4.3 The label status is missing or incorrect');
    }

    /**
     * Test `publishAction` method with force parameter (uncomplete resume).
     *
     * @return void
     */
    public function testPublishActionWithForce(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go to the new page.
        $crawler = $client->request(Request::METHOD_GET, "/restricted/resume/new");

        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => true,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Développeur Fullstack',
            'com_nairus_resumebundle_resume[translations][en][title]' => 'Fullstack Developer',
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Click on the publish button
        $crawler = $client->submit($crawler->selectButton('Publier')->form());

        $this->assertRegExp("~/restricted/resume/[0-9]+/publish~", $client->getRequest()->getRequestUri(), "1.1 The request uri is not expected");
        $this->assertRegExp("~Le CV n°[0-9]+ est incomplet !~", $crawler->filter('.message-container')->text(), "1.2 The [incomplete] flash message is not ok.");
        $this->assertContains('Si vous souhaitez quand même le publier, forcez la publication en cliquant sur le bouton "Publier".',
                $crawler->filter('#admin-container')->text(), "1.3 The [publish] message is not ok.");

        $actionsButtons = $crawler->filter(".actions");
        $this->assertCount(3, $actionsButtons, "1.4 Three actions buttons are expected");
        $this->assertContains('<i class="far fa-eye"></i>', $actionsButtons->eq(0)->html(), "1.5 The see details picto is missing");
        $this->assertContains("Voir les détails", $actionsButtons->eq(0)->text(), "1.6 The see details label is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsButtons->eq(1)->html(), "1.7 The edit picto is missing");
        $this->assertContains("Modifier", $actionsButtons->eq(1)->text(), "1.8 The edit label is not ok");
        $this->assertContains('<i class="fas fa-globe"></i>', $actionsButtons->eq(2)->html(), "1.9 The publish picto is missing");
        $this->assertContains("Publier", $actionsButtons->eq(2)->text(), "1.10 The publish label is not ok");

        $client->submit($crawler->selectButton('Publier')->form());
        $crawler = $client->followRedirect();

        $this->assertEquals("/restricted/resume/", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $resumeCards = $crawler->filter("#resume-container > div")->children();
        $actionsButtons = $resumeCards->eq(0)->filter(".actions");
        $this->assertContains("Dépublier", $actionsButtons->eq(3)->text(), "2.2 The unpublish label is not ok");
        $this->assertRegExp("~CV n°[0-9]+ publié avec succès !~", $crawler->filter('.message-container')->text(), "2.3 The [publish] flash message is not ok.");
        $this->assertContains('En ligne', $resumeCards->eq(0)->text(), '2.4 The label status is missing or incorrect');
    }

    /**
     * Test `publishAction` method with no profile.
     *
     * @return void
     */
    public function testPublishActionWithNoProfile(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Go to the new page.
        $crawler = $client->request(Request::METHOD_GET, "/restricted/resume/new");

        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => false,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Développeur Fullstack',
            'com_nairus_resumebundle_resume[translations][en][title]' => 'Fullstack Developer',
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Click on the publish button
        $client->submit($crawler->selectButton('Publier')->form());
        $crawler = $client->followRedirect();

        $this->assertRegExp("~/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "1.1 The request uri is not expected");
        $this->assertRegExp("~Il n'y a pas de profil pour le CV n°[0-9]+ ! Vous pouvez rendre le CV anonyme ou ajouter un profil.~",
                $crawler->filter('.message-container')->text(), "1.2 The [incomplete] flash message is not ok.");
    }

    /**
     * Test the dispatching of update status event.
     *
     * @return void
     */
    public function testUpdateResumeStatus(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Case 1: Add a resume and go to the show page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());
        $crawler = $client->click($crawler->selectLink("Ajouter un nouveau CV")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => true,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre FR',
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();

        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '1.1 The label status is missing or incorrect');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '1.2 The status picto is missing or incorrect');

        // Case 2: Add a resume skill
        $crawler = $client->click($crawler->selectLink("Ajouter une compétence")->link());
        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[0];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[0];
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 1,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '2.1 The label status has to remain the same');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '2.2 The status picto has to remain the same');

        // Case 3: Add an education
        $crawler = $client->click($crawler->selectLink("Ajouter une formation")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_education[diploma]" => "Diplôme",
            "com_nairus_resumebundle_education[institution]" => "Organisme",
            "com_nairus_resumebundle_education[startYear]" => "2005",
            "com_nairus_resumebundle_education[endYear]" => "2006",
            "com_nairus_resumebundle_education[translations][fr][description]" => "Description",
            "com_nairus_resumebundle_education[translations][fr][domain]" => "Domaine",
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->selectLink("Retour au CV")->link());
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '3.1 The label status has to remain the same');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '3.2 The status picto has to remain the same');

        // Case 4: Add an experience
        $crawler = $client->click($crawler->selectLink("Ajouter une expérience")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_experience[company]" => "Company",
            "com_nairus_resumebundle_experience[location]" => "Location",
            "com_nairus_resumebundle_experience[startYear]" => "2018",
            "com_nairus_resumebundle_experience[startMonth]" => "1",
            "com_nairus_resumebundle_experience[endYear]" => "2018",
            "com_nairus_resumebundle_experience[endMonth]" => "2",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => "Description",
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->selectLink("Retour au CV")->link());
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Complet Hors ligne', $adminContainer, '4.1 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-half"></i>', $adminContainer, '4.2 The status picto is incorrect');
    }

    /**
     * Test the dispatching of delete status event in resume skill delete action.
     *
     * @return void
     */
    public function testDeleteResumeStatusWithResumeSkillDeleteAction(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Go to resume show page with all content (offline incomplete)
        $crawler = $this->addCompleteOfflineResume($crawler);
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Complet Hors ligne', $adminContainer, '1.1 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-half"></i>', $adminContainer, '1.2 The status picto is incorrect');

        // Case 1: Delete a resume skill
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Supprimer")->link());
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '2.2 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '2.3 The status picto is incorrect');
    }

    /**
     * Test the dispatching of delete status event in education delete action.
     *
     * @return void
     */
    public function testDeleteResumeStatusWithEducationDeleteAction(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Go to resume show page with all content (offline incomplete)
        $crawler = $this->addCompleteOfflineResume($crawler);
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Complet Hors ligne', $adminContainer, '1.1 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-half"></i>', $adminContainer, '1.2 The status picto is incorrect');

        // Case 1: Delete an education
        $cardFooterElements = $crawler->filter("#educations-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Supprimer")->link());
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '2.2 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '2.3 The status picto is incorrect');
    }

    /**
     * Test the dispatching of delete status event in experience delete action.
     *
     * @return void
     */
    public function testDeleteResumeStatusWithExperienceDeleteAction(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Go to resume show page with all content (offline incomplete)
        $crawler = $this->addCompleteOfflineResume($crawler);
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Complet Hors ligne', $adminContainer, '1.1 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-half"></i>', $adminContainer, '1.2 The status picto is incorrect');

        // Case 1: Delete an education
        $cardFooterElements = $crawler->filter("#experiences-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Supprimer")->link());
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('Incomplet Hors ligne', $adminContainer, '2.2 The label status is incorrect');
        $this->assertContains('<i class="fas fa-thermometer-quarter"></i>', $adminContainer, '2.3 The status picto is incorrect');
    }

    /**
     * Remove all datas set
     *
     * @return void
     */
    private function removeAllDatasSet(): void {
        $loadResumeOnline = new LoadResumeOnline();
        $loadResumeOnline->remove($this->getEntityManager());
        $this->cleanDatas($this->getClient()->getContainer(), [Skill::class, SkillLevel::class]);

        // Get all admin's resumes remaining and remove them.
        $user = $this->getEntityManager()->getRepository(User::class)->findOneByUsername("admin");
        $resumes = $this->getEntityManager()->getRepository(Resume::class)->findByAuthor($user);
        foreach ($resumes as /* @var $resume Resume */ $resume) {
            foreach ($resume->getEducations() as $education) {
                $this->getEntityManager()->remove($education);
                $resume->removeEducation($education);
            }
            foreach ($resume->getExperiences() as $experience) {
                $this->getEntityManager()->remove($experience);
                $resume->removeExperience($experience);
            }
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $this->getEntityManager()->remove($resumeSkill);
                $resume->removeResumeSkill($resumeSkill);
            }
            $this->getEntityManager()->remove($resume);
        }

        // Get all author's resumes datas remaining and remove them.
        $author = $this->getEntityManager()->getRepository(User::class)->findOneByUsername("author");
        $resumes = $this->getEntityManager()->getRepository(Resume::class)->findByAuthor($author);
        foreach ($resumes as /* @var $resume Resume */ $resume) {
            foreach ($resume->getEducations() as $education) {
                $this->getEntityManager()->remove($education);
                $resume->removeEducation($education);
            }
            foreach ($resume->getExperiences() as $experience) {
                $this->getEntityManager()->remove($experience);
                $resume->removeExperience($experience);
            }
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $this->getEntityManager()->remove($resumeSkill);
                $resume->removeResumeSkill($resumeSkill);
            }
        }
        $this->getEntityManager()->flush();
    }

    /**
     * Add a resume with all dependencies.
     *
     * @param Crawler $crawler The crawler instance.
     *
     * @return Crawler The current crawler.
     */
    private function addCompleteOfflineResume(Crawler $crawler): Crawler {
        // Get the client
        $client = $this->getClient();

        // Go to admin resumes page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Add a new reume
        $crawler = $client->click($crawler->selectLink("Ajouter un nouveau CV")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_resume[anonymous]' => true,
            'com_nairus_resumebundle_resume[translations][fr][title]' => 'Titre FR',
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Add a new resume skill
        $crawler = $client->click($crawler->selectLink("Ajouter une compétence")->link());
        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[0];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[0];
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 1,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Add a new education
        $crawler = $client->click($crawler->selectLink("Ajouter une formation")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_education[diploma]" => "Diplôme",
            "com_nairus_resumebundle_education[institution]" => "Organisme",
            "com_nairus_resumebundle_education[startYear]" => "2005",
            "com_nairus_resumebundle_education[endYear]" => "2006",
            "com_nairus_resumebundle_education[translations][fr][description]" => "Description",
            "com_nairus_resumebundle_education[translations][fr][domain]" => "Domaine",
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->selectLink("Retour au CV")->link());

        // Add a new experience
        $crawler = $client->click($crawler->selectLink("Ajouter une expérience")->link());
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_experience[company]" => "Company",
            "com_nairus_resumebundle_experience[location]" => "Location",
            "com_nairus_resumebundle_experience[startYear]" => "2018",
            "com_nairus_resumebundle_experience[startMonth]" => "1",
            "com_nairus_resumebundle_experience[endYear]" => "2018",
            "com_nairus_resumebundle_experience[endMonth]" => "2",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => "Description",
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();
        return $client->click($crawler->selectLink("Retour au CV")->link());
    }

}
