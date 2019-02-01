<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test of the Profile controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ProfileControllerTest extends AbstractUserWebTestCase {

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
     * Test entity.
     *
     * @var Resume
     */
    private $onlineResume;

    /**
     * Test profile entity.
     *
     * @var Profile
     */
    private $profile;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        // Initialize components.
        parent::setUp();

        // Prepare datas test set.
        $this->loadDatas($this->getEntityManager(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeOnline()]);
        $this->onlineResume = $this->getEntityManager()->getRepository(Resume::class)->findOneByStatus(ResumeStatusEnum::ONLINE);
        $author = $this->getEntityManager()->getRepository(User::class)->findOneByUsername("author");
        $this->profile = $this->getEntityManager()->getRepository(Profile::class)->findOneByUser($author);

        $admin = $this->getEntityManager()->getRepository(User::class)->findOneByUsername("admin");
        $this->resume = new Resume();
        $this->resume->setIp("127.0.0.1")
                ->setAuthor($admin)
                ->setCurrentLocale("fr")
                ->setTitle("Titre du CV.");
        $this->getEntityManager()->persist($this->resume);
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Remove the datas set.
        $loadResumeOnline = new LoadResumeOnline();
        $loadResumeOnline->remove($this->getEntityManager());
        $this->cleanDatas([Skill::class, SkillLevel::class]);
        $admin = $this->getEntityManager()->getRepository(User::class)->findOneByUsername("admin");
        $resume = $this->getEntityManager()->getRepository(Resume::class)->findOneByAuthor($admin);
        $this->getEntityManager()->remove($resume);
        $profile = $this->getEntityManager()->getRepository(Profile::class)->findOneByUser($admin);
        if (null !== $profile) {
            $this->getEntityManager()->remove($profile);
        }
        $this->getEntityManager()->flush();

        unset($this->onlineResume, $this->resume, $this->profile);

        // Clean the container.
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

        $resumeId = $this->onlineResume->getId();
        $client->request(Request::METHOD_GET, "/restricted/profile/new/$resumeId/resume");
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

        $client->request(Request::METHOD_GET, "/restricted/profile/new/999999/resume");
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test edit action with bad credential.
     *
     * @return void
     */
    public function testEditActionWithBadCredential(): void {
        $this->logInAuthor();
        $client = $this->getClient();

        $client->request(Request::METHOD_GET, sprintf("/restricted/profile/%d/edit/%d/resume", $this->profile->getId(), $this->onlineResume->getId()));
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test delete action with bad credential.
     *
     * @return void
     */
    public function testDeleteActionWithBadCredential(): void {
        $this->logInAuthor();
        $client = $this->getClient();

        $client->request(Request::METHOD_DELETE, sprintf("/restricted/profile/%d/delete/%d/resume", $this->profile->getId(), $this->onlineResume->getId()));
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test complete scenario in fr.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Case 1: Add a profile.
        $profileCard = $crawler->filter("#profile-card");
        $this->assertNotNull($profileCard, "1.1 The resume detail has to display the profile card.");
        $crawler = $client->click($profileCard->selectLink("Ajouter un profil")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The response status code expected is not ok.");
        $this->assertRegExp("~^/restricted/profile/new/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Ajout du profil pour mes CVs", $crawler->filter("html > head > title")->text(), "1.4 The title expected is not ok");
        $this->assertEquals("Ajout du profil pour mes CVs", $crawler->filter("h1")->text(), "1.5 The h1 expected is not ok");
        // Get the form
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_profile"]');
        $this->assertEquals(9, $form->filter(".form-group")->count(), "1.6 Nine form group elements are expected");
        $this->assertContains("Nom", $form->text(), "1.7 The form has to contain lastName label.");
        $this->assertContains("Prénom", $form->text(), "1.8 The form has to contain firstName label.");
        $this->assertContains("Tél. fixe", $form->text(), "1.9 The form has to contain phone label.");
        $this->assertContains("Tél. portable", $form->text(), "1.10 The form has to contain cell label.");
        $this->assertContains("Adresse", $form->text(), "1.11 The form has to contain address label.");
        $this->assertContains("Complément d'adresse", $form->text(), "1.8 The form has to contain addressAddition label.");
        $this->assertContains("Ville", $form->text(), "1.9 The form has to contain city label.");
        $this->assertContains("Code postal", $form->text(), "1.10 The form has to contain zip label.");
        $this->assertContains("Pays", $form->text(), "1.10 The form has to contain country label.");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.14 Two cta buttons are expected.");
        $this->assertContains("Retour au CV", $actionsElements->eq(0)->text(), "1.15 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "1.16 The first button pico is missing.");
        $this->assertContains("Sauvegarder", $actionsElements->eq(1)->text(), "1.17 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "1.18 The second button pico is missing.");
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_profile[lastName]" => "Surian",
            "com_nairus_resumebundle_profile[firstName]" => "Nicolas",
            "com_nairus_resumebundle_profile[phone]" => "04 01 02 03 04",
            "com_nairus_resumebundle_profile[address]" => "1 place de l'hôtel de ville",
            "com_nairus_resumebundle_profile[city]" => "Marseille",
            "com_nairus_resumebundle_profile[zip]" => "13001",
        ]);

        // Submit the form and return to the resume show page
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Verify the profile addition
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.19 The response status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "1.20 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "1.21 The [add] flash message is missing.");
        $this->assertContains("Profil ajouté avec succès !", $crawler->filter('.message-container')->text(), "1.21 The [add] flash message is not ok.");
        $profileCard = $crawler->filter("#profile-card");
        $this->assertContains("Nicolas Surian", $profileCard->text(), "1.22 The profile card has to contain the firstName and the lastName");
        $this->assertContains("1 place de l'hôtel de ville", $profileCard->text(), "1.23 The profile card has to contain the address");
        $this->assertContains("13001 Marseille", $profileCard->text(), "1.24 The profile card has to contain the zip code and the city");
        $this->assertContains("04 01 02 03 04", $profileCard->text(), "1.25 The profile card has to contain the phone");
        $this->assertContains('<i class="fas fa-phone"></i>', $profileCard->html(), "1.26 The profile card has to contain the phone picto");
        $actionsButtons = $profileCard->filter(".card-footer")->children();
        $this->assertCount(2, $actionsButtons, "1.27 The buttons are expected");
        $this->assertContains("Modifier le profil", $actionsButtons->eq(0)->text(), "1.28 The first button label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsButtons->eq(0)->html(), "1.29 The first button picto expected is not ok");
        $this->assertContains("Supprimer le profil", $actionsButtons->eq(1)->text(), "1.30 The second button  label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsButtons->eq(1)->html(), "1.31 The second button picto expected is not ok");

        // Case 2: Edit the profile.
        $crawler = $client->click($profileCard->selectLink("Modifier le profil")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The response status code expected is not ok.");
        $this->assertRegExp("~^/restricted/profile/[0-9]+/edit/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Modification de mon profil de CV", $crawler->filter("html > head > title")->text(), "2.3 The title expected is not ok");
        $this->assertEquals("Modification de mon profil de CV", $crawler->filter("h1")->text(), "2.4 The h1 expected is not ok");
        // Get the form
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_profile"]');
        $this->assertEquals(9, $form->filter(".form-group")->count(), "2.5 Nine form group elements are expected");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "2.6 Two cta buttons are expected.");
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_profile[lastName]" => "Surian",
            "com_nairus_resumebundle_profile[firstName]" => "Nicolas",
            "com_nairus_resumebundle_profile[phone]" => "04 01 02 03 04",
            "com_nairus_resumebundle_profile[cell]" => "07 01 02 03 04",
            "com_nairus_resumebundle_profile[address]" => "1 place de l'hôtel de ville",
            "com_nairus_resumebundle_profile[addressAddition]" => "RDC porte droite",
            "com_nairus_resumebundle_profile[city]" => "Marseille",
            "com_nairus_resumebundle_profile[zip]" => "13001",
            "com_nairus_resumebundle_profile[country]" => "France",
        ]);

        // Submit the form and return to the resume show page
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Verify the profile addition
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.6 The response status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "2.7 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.8 The [edit] flash message is missing.");
        $this->assertContains("Profil modifié avec succès !", $crawler->filter('.message-container')->text(), "2.9 The [edit] flash message is not ok.");
        $profileCard = $crawler->filter("#profile-card");
        $this->assertContains("07 01 02 03 04", $profileCard->text(), "2.10 The form has to contain cell label.");
        $this->assertContains('<i class="fas fa-mobile-alt"></i>', $profileCard->html(), "2.11 The form has to contain cell picto.");
        $this->assertContains("RDC porte droite", $profileCard->text(), "2.12 The form has to contain addressAddition label.");
        $this->assertContains("France", $profileCard->text(), "2.13 The form has to contain country label.");

        // Case 3: Delete the profile.
        $client->submit($profileCard->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.1 The response status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.3 The [delete] flash message is missing.");
        $this->assertContains("Profil supprimé avec succès !", $crawler->filter('.message-container')->text(), "3.4 The [delete] flash message is not ok.");
        $actionsButtons = $crawler->filter("#profile-card .card-body")->children();
        $this->assertCount(1, $actionsButtons, "3.5 One button is expected");
        $this->assertContains("Ajouter un profil", $actionsButtons->eq(0)->text(), "3.6 The first button label expected is not ok");
        $this->assertContains('<i class="fas fa-plus"></i>', $actionsButtons->eq(0)->html(), "3.7 The first button picto expected is not ok");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        $crawler = $this->logInAdmin("en");
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("My Resumes")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Show details")->link());

        // Case 1: Add a profile.
        $profileCard = $crawler->filter("#profile-card");
        $this->assertNotNull($profileCard, "1.1 The resume detail has to display the profile card.");
        $crawler = $client->click($profileCard->selectLink("Add a profile")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The response status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/profile/new/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Add a profile for my resumes", $crawler->filter("html > head > title")->text(), "1.4 The title expected is not ok");
        $this->assertEquals("Add a profile for my resumes", $crawler->filter("h1")->text(), "1.5 The h1 expected is not ok");
        // Get the form
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_profile"]');
        $this->assertEquals(9, $form->filter(".form-group")->count(), "1.6 Nine form group elements are expected");
        $formGroups = $form->filter(".form-group");
        $this->assertContains("Last name", $formGroups->eq(0)->text(), "1.7 The form has to contain lastName label.");
        $this->assertContains("First name", $formGroups->eq(1)->text(), "1.8 The form has to contain firstName label.");
        $this->assertContains("Landline phone", $formGroups->eq(2)->text(), "1.9 The form has to contain phone label.");
        $this->assertContains("Mobile phone", $formGroups->eq(3)->text(), "1.10 The form has to contain cell label.");
        $this->assertContains("Address", $formGroups->eq(4)->text(), "1.11 The form has to contain address label.");
        $this->assertContains("Additional address", $formGroups->eq(5)->text(), "1.8 The form has to contain addressAddition label.");
        $this->assertContains("City", $formGroups->eq(6)->text(), "1.9 The form has to contain city label.");
        $this->assertContains("Zip code", $formGroups->eq(7)->text(), "1.10 The form has to contain zip label.");
        $this->assertContains("Country", $formGroups->eq(8)->text(), "1.10 The form has to contain country label.");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.14 Two cta buttons are expected.");
        $this->assertContains("Return to the resume", $actionsElements->eq(0)->text(), "1.15 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "1.16 The first button pico is missing.");
        $this->assertContains("Save", $actionsElements->eq(1)->text(), "1.17 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "1.18 The second button pico is missing.");
        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_profile[lastName]" => "Surian",
            "com_nairus_resumebundle_profile[firstName]" => "Nicolas",
            "com_nairus_resumebundle_profile[phone]" => "04 01 02 03 04",
            "com_nairus_resumebundle_profile[address]" => "1 place de l'hôtel de ville",
            "com_nairus_resumebundle_profile[city]" => "Marseille",
            "com_nairus_resumebundle_profile[zip]" => "13001",
        ]);

        // Submit the form and return to the resume show page
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Verify the profile addition
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.19 The response status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "1.20 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "1.21 The [add] flash message is missing.");
        $this->assertContains("Profile added successfully!", $crawler->filter('.message-container')->text(), "1.21 The [add] flash message is not ok.");
        $profileCard = $crawler->filter("#profile-card");
        $this->assertContains("Nicolas Surian", $profileCard->text(), "1.22 The profile card has to contain the firstName and the lastName");
        $this->assertContains("1 place de l'hôtel de ville", $profileCard->text(), "1.23 The profile card has to contain the address");
        $this->assertContains("Marseille 13001", $profileCard->text(), "1.24 The profile card has to contain the zip code and the city");
        $this->assertContains("04 01 02 03 04", $profileCard->text(), "1.25 The profile card has to contain the phone");
        $this->assertContains('<i class="fas fa-phone"></i>', $profileCard->html(), "1.26 The profile card has to contain the phone picto");
        $actionsButtons = $profileCard->filter(".card-footer")->children();
        $this->assertCount(2, $actionsButtons, "1.27 The buttons are expected");
        $this->assertContains("Edit the profile", $actionsButtons->eq(0)->text(), "1.28 The first button label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsButtons->eq(0)->html(), "1.29 The first button picto expected is not ok");
        $this->assertContains("Delete the profile", $actionsButtons->eq(1)->text(), "1.30 The second button  label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsButtons->eq(1)->html(), "1.31 The second button picto expected is not ok");

        // Case 2: Edit the profile.
        $crawler = $client->click($profileCard->selectLink("Edit the profile")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The response status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/profile/[0-9]+/edit/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Modification of my resumes's profile", $crawler->filter("html > head > title")->text(), "2.3 The title expected is not ok");
        $this->assertEquals("Modification of my resumes's profile", $crawler->filter("h1")->text(), "2.4 The h1 expected is not ok");
        // Get the form
        $form = $crawler->filterXPath('//html/body/main/div/div/form[@name="com_nairus_resumebundle_profile"]');
        $this->assertEquals(9, $form->filter(".form-group")->count(), "2.5 Nine form group elements are expected");
        $actionsElements = $form->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "2.6 Two cta buttons are expected.");
        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_profile[lastName]" => "Surian",
            "com_nairus_resumebundle_profile[firstName]" => "Nicolas",
            "com_nairus_resumebundle_profile[phone]" => "04 01 02 03 04",
            "com_nairus_resumebundle_profile[cell]" => "07 01 02 03 04",
            "com_nairus_resumebundle_profile[address]" => "1 place de l'hôtel de ville",
            "com_nairus_resumebundle_profile[addressAddition]" => "RDC porte droite",
            "com_nairus_resumebundle_profile[city]" => "Marseille",
            "com_nairus_resumebundle_profile[zip]" => "13001",
            "com_nairus_resumebundle_profile[country]" => "France",
        ]);

        // Submit the form and return to the resume show page
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Verify the profile addition
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.6 The response status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "2.7 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.8 The [edit] flash message is missing.");
        $this->assertContains("Profile modified successfully!", $crawler->filter('.message-container')->text(), "2.9 The [edit] flash message is not ok.");
        $profileCard = $crawler->filter("#profile-card");
        $this->assertContains("07 01 02 03 04", $profileCard->text(), "2.10 The form has to contain cell label.");
        $this->assertContains('<i class="fas fa-mobile-alt"></i>', $profileCard->html(), "2.11 The form has to contain cell picto.");
        $this->assertContains("RDC porte droite", $profileCard->text(), "2.12 The form has to contain addressAddition label.");
        $this->assertContains("France", $profileCard->text(), "2.13 The form has to contain country label.");

        // Case 3: Delete the profile.
        $client->submit($profileCard->selectButton('Delete the profile')->form());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.1 The response status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.3 The [delete] flash message is missing.");
        $this->assertContains("Profile deleted successfully!", $crawler->filter('.message-container')->text(), "3.4 The [delete] flash message is not ok.");
        $actionsButtons = $crawler->filter("#profile-card .card-body")->children();
        $this->assertCount(1, $actionsButtons, "3.5 One button is expected");
        $this->assertContains("Add a profile", $actionsButtons->eq(0)->text(), "3.6 The first button label expected is not ok");
        $this->assertContains('<i class="fas fa-plus"></i>', $actionsButtons->eq(0)->html(), "3.7 The first button picto expected is not ok");
    }

    /**
     * Test the validation of new profile's form.
     *
     * @return void
     */
    public function testValidateNewFormForProfile(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Add a profile.
        $profileCard = $crawler->filter("#profile-card");
        $crawler = $client->click($profileCard->selectLink("Ajouter un profil")->link());

        // Get the form
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_profile[lastName]" => " ",
            "com_nairus_resumebundle_profile[firstName]" => " ",
            "com_nairus_resumebundle_profile[phone]" => "azerty",
            "com_nairus_resumebundle_profile[cell]" => "",
            "com_nairus_resumebundle_profile[address]" => " ",
            "com_nairus_resumebundle_profile[city]" => " ",
            "com_nairus_resumebundle_profile[zip]" => "qsdfg",
        ]);

        // Submit the form and return to the resume show page
        $crawler = $client->submit($form);
        $this->assertRegExp("~^/restricted/profile/new/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertCount(6, $crawler->filter(".is-invalid"), "1.2 The required fields have to be marked as invalid.");
        $this->assertCount(6, $crawler->filter(".invalid-feedback"), "1.3 All errors messages have to be displayed");
    }

    /**
     * Test the validation of edit profile's form.
     *
     * @return void
     */
    public function testValidateEditFormForProfile(): void {
        $crawler = $this->logInAuthor();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Edit the profile.
        $profileCard = $crawler->filter("#profile-card");
        $crawler = $client->click($profileCard->selectLink("Modifier le profil")->link());

        // Get the form
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_profile[lastName]" => " ",
            "com_nairus_resumebundle_profile[firstName]" => " ",
            "com_nairus_resumebundle_profile[phone]" => "azerty",
            "com_nairus_resumebundle_profile[address]" => " ",
            "com_nairus_resumebundle_profile[city]" => " ",
            "com_nairus_resumebundle_profile[zip]" => "qsdfg",
        ]);

        // Submit the form and return to the resume show page
        $crawler = $client->submit($form);
        $this->assertRegExp("~^/restricted/profile/[0-9]+/edit/[0-9]+/resume$~", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The response status code expected is not ok.");
        $this->assertCount(6, $crawler->filter(".is-invalid"), "1.3 The required fields have to be marked as invalid.");
        $this->assertCount(6, $crawler->filter(".invalid-feedback"), "1.4 All errors messages have to be displayed");
    }

}
