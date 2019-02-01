<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test of restricted Experience controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceControllerTest extends AbstractUserWebTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * Test entity.
     *
     * @var Resume
     */
    private $resume;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        parent::setUp();

        // Prepare datas test set.
        $this->loadDatas($this->getEntityManager(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);

        $this->resume = $this->getEntityManager()->getRepository(Resume::class)->findOneByStatus(ResumeStatusEnum::ONLINE);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $loadResumeOnline = new LoadResumeOnline();
        $loadResumeOnline->remove($this->getEntityManager());
        $this->cleanDatas([Skill::class, SkillLevel::class]);
        unset($this->resume);
        parent::tearDown();
    }

    /**
     * Test new action with bad credential.
     *
     * @return void
     */
    public function testNewActionWithBadCredential(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        $resumeId = $this->resume->getId();
        $client->request(Request::METHOD_GET, "/restricted/experience/$resumeId/new");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test new action with bad credential.
     *
     * @return void
     */
    public function testNewActionWithResumeNotFound(): void {
        $this->logInModerator();
        $client = $this->getClient();

        $client->request(Request::METHOD_GET, "/restricted/experience/999999/new");
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test edit action with bad credential.
     *
     * @return void
     */
    public function testEditActionWithBadCredential(): void {
        $this->logInAdmin();
        $client = $this->getClient();
        /* @var $experience Experience */
        $experience = $this->resume->getExperiences()->first();
        $id = $experience->getId();
        $client->request(Request::METHOD_GET, "/restricted/experience/$id/edit");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test show action with bad credential.
     *
     * @return void
     */
    public function testShowActionWithBadCredential(): void {
        $this->logInAdmin();
        $client = $this->getClient();
        /* @var $experience Experience */
        $experience = $this->resume->getExperiences()->first();
        $id = $experience->getId();
        $client->request(Request::METHOD_GET, "/restricted/experience/$id/show");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test delete action with bad credential.
     *
     * @return void
     */
    public function testDeleteActionWithBadCredential(): void {
        $this->logInAdmin();
        $client = $this->getClient();
        /* @var $experience Experience */
        $experience = $this->resume->getExperiences()->first();
        $id = $experience->getId();
        $client->request(Request::METHOD_DELETE, "/restricted/experience/$id/delete");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test complete scenario in fr.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        $crawler = $this->logInAuthor();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Click on the add experience button.
        $crawler = $client->click($crawler->selectLink("Ajouter une expérience")->link());

        // Case 1 : Add a new experience
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The response status code expected is not ok.");
        $this->assertRegExp("~/restricted/experience/[0-9]+/new~", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertContains("Ajout d'une expérience", $crawler->filter("html > head > title")->text(), "1.3 The title expected is not ok");
        $this->assertEquals("Ajout d'une expérience", $crawler->filter("h1")->text(), "1.4 The h1 expected is not ok");
        // Get the form.
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_experience"]');
        $this->assertEquals(10, $form->filter(".form-group")->count(), "1.5 Ten form group elements are expected");
        $this->assertContains("Société", $form->text(), "1.6 The form has to contain company label.");
        $this->assertContains("Lieu", $form->text(), "1.7 The form has to contain location label.");
        $this->assertContains("Mois de début", $form->text(), "1.8 The form has to contain start-month label.");
        $this->assertContains("Année de début", $form->text(), "1.9 The form has to contain start-year label.");
        $this->assertContains("Mois de fin", $form->text(), "1.10 The form has to contain end-month label.");
        $this->assertContains("Année de fin", $form->text(), "1.11 The form has to contain end-year label.");
        $this->assertContains("En poste ?", $form->text(), "1.12 The form has to contain curent-job label.");
        $this->assertContains("Traductions", $form->text(), "1.13 The form has to contain translations label.");
        $this->assertContains("Description", $form->text(), "1.14 The form has to contain description label.");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.15 Two cta buttons are expected.");
        $this->assertContains("Retour au CV", $actionsElements->eq(0)->text(), "1.16 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "1.17 The first button pico is missing.");
        $this->assertContains("Sauvegarder", $actionsElements->eq(1)->text(), "1.18 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "1.19 The second button pico is missing.");
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

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 2: Go the show page
        $this->assertRegExp("~^/restricted/experience/[0-9]+/show~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $this->assertRegExp("~Détail de l'expérience n°[0-9+]~", $crawler->filter('html > head > title')->text(), '2.2 The page title excepted is not OK');
        $this->assertRegExp("~Détail de l'expérience n°[0-9+]~", $crawler->filter('h1')->text(), '2.3 The h1 label excepted is not OK');
        $this->assertContains("Company", $crawler->filter(".card-header")->text(), "2.4 The card-header has to contain company.");
        $this->assertContains("Location", $crawler->filter(".card-header")->text(), "2.5 The card-header has to contain location.");
        $this->assertContains("Janvier 2018", $crawler->filter(".card-body .card-text")->eq(0)->text(), "2.6 The card-body has to contain start month and year.");
        $this->assertContains("Février 2018", $crawler->filter(".card-body .card-text")->eq(0)->text(), "2.7 The card-body has to contain end month and year.");
        $this->assertContains("Description", $crawler->filter(".card-body .card-text")->eq(1)->text(),
                "2.8 The card-body has to contain description.");
        $cardFooterElements = $crawler->filter(".card-footer")->children();
        $this->assertCount(3, $cardFooterElements, "2.9 The card-footer has to contain three elements.");
        $this->assertContains("Retour au CV", $cardFooterElements->eq(0)->text(), "2.10 The return label excepted is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $cardFooterElements->eq(0)->html(), "2.11 The return picto excepted is not ok.");
        $this->assertContains("Modifier", $cardFooterElements->eq(1)->text(), "2.12 The edit label excepted is not ok.");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $cardFooterElements->eq(1)->html(), "2.13 The edit picto excepted is not ok.");
        $this->assertContains("Supprimer", $cardFooterElements->eq(2)->text(), "2.14 The delete label excepted is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $cardFooterElements->eq(2)->html(), "2.15 The delete picto excepted is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.16 The [add] flash message is missing.");
        $this->assertContains("Expérience ajoutée avec succès !", $crawler->filter('.message-container')->text(), "2.17 The [add] flash message is not ok.");

        // Go on the resume edit page
        $crawler = $client->click($crawler->selectLink("Modifier")->link());

        // Case 3: Edit the experience
        $this->assertRegExp("~^/restricted/experience/[0-9]+/edit~", $client->getRequest()->getRequestUri(), "3.1 The request uri expected is not ok.");
        $this->assertRegExp("~Modification de l'expérience n°[0-9+]~", $crawler->filter('html > head > title')->text(), '3.2 The page title excepted is not OK');
        $this->assertRegExp("~Modification de l'expérience n°[0-9+]~", $crawler->filter('h1')->text(), '3.3 The h1 label excepted is not OK');
        $actionsElements = $crawler->filter(".actions")->children();
        $this->assertCount(3, $actionsElements, "3.4 Two cta buttons are expected.");
        $this->assertContains("Retour", $actionsElements->eq(0)->text(), "3.5 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "3.6 The first button pico is missing.");
        $this->assertContains("Voir les détails", $actionsElements->eq(1)->text(), "3.7 The second button label is not ok.");
        $this->assertContains('<i class="far fa-eye"></i>', $actionsElements->eq(1)->html(), "3.8 The second button pico is missing.");
        $this->assertContains("Sauvegarder", $actionsElements->eq(2)->text(), "3.9 The third button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(2)->html(), "3.10 The third button pico is missing.");
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_experience[company]" => "Company 2",
            "com_nairus_resumebundle_experience[location]" => "Location 2",
            "com_nairus_resumebundle_experience[startYear]" => "2007",
            "com_nairus_resumebundle_experience[startMonth]" => "10",
            "com_nairus_resumebundle_experience[endYear]" => "2009",
            "com_nairus_resumebundle_experience[endMonth]" => "3",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => "Description FR",
            "com_nairus_resumebundle_experience[translations][en][description]" => "Description EN",
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 4: Return to the show page
        $this->assertRegExp("~^/restricted/experience/[0-9]+/show~", $client->getRequest()->getRequestUri(), "4.1 The request uri expected is not ok.");
        $this->assertContains("Company 2", $crawler->filter(".card-header")->text(), "4.2 The card-header has to contain company.");
        $this->assertContains("Location 2", $crawler->filter(".card-header")->text(), "4.3 The card-header has to contain location.");
        $this->assertContains("Octobre 2007", $crawler->filter(".card-body .card-text")->eq(0)->text(), "4.4 The start month and year have to be updated.");
        $this->assertContains("Mars 2009", $crawler->filter(".card-body .card-text")->eq(0)->text(),
                "4.5 The end month and year have to be updated.");
        $this->assertContains("Description FR", $crawler->filter(".card-body .card-text")->eq(1)->text(),
                "4.6 The description has to be updated.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "4.7 The [edit] flash message is missing.");
        $this->assertRegExp("~Expérience n°[0-9]+ modifiée avec succès !~", $crawler->filter('.message-container')->text(), "4.8 The [edit] flash message is not ok.");

        // Case 5: Delete the experience
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "5.1 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "5.2 The [delete] flash message is missing.");
        $this->assertRegExp("~Expérience n°[0-9]+ supprimée avec succès !~", $crawler->filter('.message-container')->text(), "5.3 The [delete] flash message is not ok.");
        $this->assertNotContains("Company 2", $crawler->filter("#educations-content"), "5.4 The experience should not appear on the page");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        $crawler = $this->logInAuthor("en");
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("My Resumes")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Show details")->link());

        // Click on the add experience button.
        $crawler = $client->click($crawler->selectLink("Add an experience")->link());

        // Case 1 : Add a new experience
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The response status code expected is not ok.");
        $this->assertRegExp("~/en/restricted/experience/[0-9]+/new~", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertContains("Add an experience", $crawler->filter("html > head > title")->text(), "1.3 The title expected is not ok");
        $this->assertEquals("Add an experience", $crawler->filter("h1")->text(), "1.4 The h1 expected is not ok");
        // Get the form.
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_experience"]');
        $this->assertEquals(10, $form->filter(".form-group")->count(), "1.5 Ten form group elements are expected");
        $this->assertContains("Company", $form->text(), "1.6 The form has to contain company label.");
        $this->assertContains("Location", $form->text(), "1.7 The form has to contain location label.");
        $this->assertContains("Start month", $form->text(), "1.8 The form has to contain start-month label.");
        $this->assertContains("Start year", $form->text(), "1.9 The form has to contain start-year label.");
        $this->assertContains("End month", $form->text(), "1.10 The form has to contain end-month label.");
        $this->assertContains("End year", $form->text(), "1.11 The form has to contain end-year label.");
        $this->assertContains("Current job?", $form->text(), "1.12 The form has to contain curent-job label.");
        $this->assertContains("Translations", $form->text(), "1.13 The form has to contain translations label.");
        $this->assertContains("Description", $form->text(), "1.14 The form has to contain description label.");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.15 Two cta buttons are expected.");
        $this->assertContains("Return to the resume", $actionsElements->eq(0)->text(), "1.16 The first button label is not ok.");
        $this->assertContains("Save", $actionsElements->eq(1)->text(), "1.17 The second button label is not ok.");
        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_experience[company]" => "Company",
            "com_nairus_resumebundle_experience[location]" => "Location",
            "com_nairus_resumebundle_experience[startYear]" => "2018",
            "com_nairus_resumebundle_experience[startMonth]" => "1",
            "com_nairus_resumebundle_experience[endYear]" => "2018",
            "com_nairus_resumebundle_experience[endMonth]" => "2",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => "Description",
        ]);

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 2: Go the show page
        $this->assertRegExp("~^/en/restricted/experience/[0-9]+/show~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $this->assertRegExp("~Detail of the experience No [0-9+]~", $crawler->filter('html > head > title')->text(), '2.2 The page title excepted is not OK');
        $this->assertRegExp("~Detail of the experience No [0-9+]~", $crawler->filter('h1')->text(), '2.3 The h1 label excepted is not OK');
        $this->assertContains("Company", $crawler->filter(".card-header")->text(), "2.4 The card-header has to contain company.");
        $this->assertContains("Location", $crawler->filter(".card-header")->text(), "2.5 The card-header has to contain location.");
        $this->assertContains("January 2018", $crawler->filter(".card-body .card-text")->eq(0)->text(), "2.6 The card-body has to contain start month and year.");
        $this->assertContains("February 2018", $crawler->filter(".card-body .card-text")->eq(0)->text(), "2.7 The card-body has to contain end month and year.");
        $this->assertContains("Description", $crawler->filter(".card-body .card-text")->eq(1)->text(),
                "2.8 The card-body has to contain description.");
        $cardFooterElements = $crawler->filter(".card-footer")->children();
        $this->assertCount(3, $cardFooterElements, "2.9 The card-footer has to contain three elements.");
        $this->assertContains("Return to the resume", $cardFooterElements->eq(0)->text(), "2.10 The return label excepted is not ok.");
        $this->assertContains("Edit", $cardFooterElements->eq(1)->text(), "2.11 The edit label excepted is not ok.");
        $this->assertContains("Delete", $cardFooterElements->eq(2)->text(), "2.12 The delete label excepted is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.13 The [add] flash message is missing.");
        $this->assertContains("Experience added successfully!", $crawler->filter('.message-container')->text(), "2.14 The [add] flash message is not ok.");

        // Go on the resume edit page
        $crawler = $client->click($crawler->selectLink("Edit")->link());

        // Case 3: Edit the experience
        $this->assertRegExp("~^/en/restricted/experience/[0-9]+/edit~", $client->getRequest()->getRequestUri(), "3.1 The request uri expected is not ok.");
        $this->assertRegExp("~Modification of the experience No [0-9+]~", $crawler->filter('html > head > title')->text(), '3.2 The page title excepted is not OK');
        $this->assertRegExp("~Modification of the experience No [0-9+]~", $crawler->filter('h1')->text(), '3.3 The h1 label excepted is not OK');
        $actionsElements = $crawler->filter(".actions")->children();
        $this->assertCount(3, $actionsElements, "3.4 Two cta buttons are expected.");
        $this->assertContains("Return", $actionsElements->eq(0)->text(), "3.5 The first button label is not ok.");
        $this->assertContains("Show details", $actionsElements->eq(1)->text(), "3.6 The second button label is not ok.");
        $this->assertContains("Save", $actionsElements->eq(2)->text(), "3.7 The third button label is not ok.");
        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_experience[company]" => "Company 2",
            "com_nairus_resumebundle_experience[location]" => "Location 2",
            "com_nairus_resumebundle_experience[startYear]" => "2007",
            "com_nairus_resumebundle_experience[startMonth]" => "10",
            "com_nairus_resumebundle_experience[endYear]" => "2009",
            "com_nairus_resumebundle_experience[endMonth]" => "3",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => "Description FR",
            "com_nairus_resumebundle_experience[translations][en][description]" => "Description EN",
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 4: Return to the show page
        $this->assertRegExp("~^/en/restricted/experience/[0-9]+/show~", $client->getRequest()->getRequestUri(), "4.1 The request uri expected is not ok.");
        $this->assertContains("Company 2", $crawler->filter(".card-header")->text(), "4.2 The card-header has to contain company.");
        $this->assertContains("Location 2", $crawler->filter(".card-header")->text(), "4.3 The card-header has to contain location.");
        $this->assertContains("October 2007", $crawler->filter(".card-body .card-text")->eq(0)->text(), "4.4 The start month and year have to be updated.");
        $this->assertContains("March 2009", $crawler->filter(".card-body .card-text")->eq(0)->text(),
                "4.5 The end month and year have to be updated.");
        $this->assertContains("Description EN", $crawler->filter(".card-body .card-text")->eq(1)->text(),
                "4.6 The description has to be updated.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "4.7 The [edit] flash message is missing.");
        $this->assertRegExp("~Experience No. [0-9]+ modified successfully!~", $crawler->filter('.message-container')->text(), "4.8 The [edit] flash message is not ok.");

        // Case 5: Delete the experience
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "5.1 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "5.2 The [delete] flash message is missing.");
        $this->assertRegExp("~Experience No. [0-9]+ deleted successfully!~", $crawler->filter('.message-container')->text(), "5.3 The [delete] flash message is not ok.");
        $this->assertNotContains("Company 2", $crawler->filter("#educations-content"), "5.4 The experience should not appear on the page");
    }

    /**
     * Test delete from resume show page.
     *
     * @return void
     */
    public function testDeleteFromResumeShowPage(): void {
        $crawler = $this->logInModerator();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('En ligne', $adminContainer, '1.1 The resume status expected is not ok');
        $this->assertContains('<i class="fas fa-thermometer-full"></i>', $adminContainer, '1.3 The resume status picto expected is not ok');


        // Delete the education.
        $educationContent = $crawler->filter("#experiences-content");
        $crawler = $client->click($educationContent->selectLink("Supprimer")->link());

        $this->assertRegExp("~/restricted/experience/[0-9]+/delete~", $client->getRequest()->getRequestUri(), "2.1 The request uri expected is not ok.");
        $this->assertRegExp("~Suppression de l'expérience n°[0-9]+~", $crawler->filter("html > head > title")->text(), "2.2 The title expected is not ok");
        $this->assertRegExp("~Suppression de l'expérience n°[0-9]+~", $crawler->filter("h1")->text(), "2.3 The h1 expected is not ok");
        $this->assertRegExp('~Êtes\-vous sûr de vouloir supprimer l\'expérience ".*" \?~', $crawler->filter("#admin-container")->text(), "2.4 The confirm label expected is not ok");
        $actionButtons = $crawler->filter("#admin-container .actions")->children();
        $this->assertCount(3, $actionButtons, "2.5 Three action buttons are expected.");
        $this->assertContains("Retour au CV", $actionButtons->eq(0)->text(), "2.6 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionButtons->eq(0)->html(), "2.7 The first button pico is missing.");
        $this->assertContains("Voir les détails", $actionButtons->eq(1)->text(), "2.8 The second button label is not ok.");
        $this->assertContains('<i class="far fa-eye"></i>', $actionButtons->eq(1)->html(), "2.9 The second button pico is missing.");
        $this->assertContains("Supprimer", $actionButtons->eq(2)->text(), "2.10 The delete label excepted is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionButtons->eq(2)->html(), "1.11 The delete picto excepted is not ok.");

        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "3.1 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.2 The [delete] flash message is missing.");
        $this->assertRegExp("~Expérience n°[0-9]+ supprimée avec succès !~", $crawler->filter('.message-container')->text(), "3.3 The [delete] flash message is not ok.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains('En ligne', $adminContainer, '3.4 The resume status label expected is not ok');
        $this->assertContains('<i class="fas fa-thermometer-full"></i>', $adminContainer, '3.5 The resume status picto expected is not ok');
    }

    /**
     * Test the validation of the new form.
     *
     * @return void
     */
    public function testValidateNewForm(): void {
        $crawler = $this->logInAuthor();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Click on the add experience button.
        $crawler = $client->click($crawler->selectLink("Ajouter une expérience")->link());

        // Submit form with bad values
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_experience[company]" => " ",
            "com_nairus_resumebundle_experience[location]" => " ",
            "com_nairus_resumebundle_experience[startYear]" => "2018",
            "com_nairus_resumebundle_experience[startMonth]" => "1",
            "com_nairus_resumebundle_experience[endYear]" => "2017",
            "com_nairus_resumebundle_experience[endMonth]" => "",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => " ",
        ]);

        $crawler = $client->submit($form);

        $this->assertRegExp("~/restricted/experience/[0-9]+/new~", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The response status code expected is not ok.");

        // Verify if there are some errors
        $this->assertCount(5, $crawler->filter(".is-invalid"), "2.1 The form has to show 5 inputs in error.");
        $this->assertCount(5, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 5 errors message.");
    }

    /**
     * Test the validation of edit form.
     *
     * @return void
     */
    public function testValidateEditForm(): void {
        $crawler = $this->logInModerator();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Click on the add experience button.
        $crawler = $client->click($crawler->filter("#experiences-content")->selectLink("Modifier")->link());

        // Submit form with bad values
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_experience[company]" => " ",
            "com_nairus_resumebundle_experience[location]" => " ",
            "com_nairus_resumebundle_experience[startYear]" => "2018",
            "com_nairus_resumebundle_experience[startMonth]" => "1",
            "com_nairus_resumebundle_experience[endYear]" => "2017",
            "com_nairus_resumebundle_experience[endMonth]" => "",
            "com_nairus_resumebundle_experience[currentJob]" => false,
            "com_nairus_resumebundle_experience[translations][fr][description]" => " ",
        ]);

        $crawler = $client->submit($form);
        $this->assertRegExp("~^/restricted/experience/[0-9]+/edit~", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The response status code expected is not ok.");

        // Verify if there are some errors
        $this->assertCount(5, $crawler->filter(".is-invalid"), "2.1 The form has to show 5 inputs in error.");
        $this->assertCount(5, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 5 errors message.");
    }

}
