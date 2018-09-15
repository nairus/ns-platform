<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;

/**
 * Skill controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillControllerTest extends AbstractUserWebTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * Test index action with bad credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\SkillController::indexAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testIndexActionWithBadCredentials(): void {
        $this->logInModerator();
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");

        // Try to reach skill index action.
        $client->request("GET", "/admin/skill/");
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
    }

    /**
     * Test index action with good credentials.
     *
     * @return void
     */
    public function testIndexActionWithGoodCredentials(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");

        // Click on nav-admin button
        $client->click($crawler->selectLink("Gestion des compétences")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
        $this->assertEquals("/admin/skill", $client->getRequest()->getRequestUri(), "3. The request uri expected is not ok.");
    }

    /**
     * Test HTTP Exceptions.
     *
     * @return void
     */
    public function testIndexActionHttpException(): void {
        $this->logInAdmin();
        $client = $this->getClient();

        // Bad request
        $client->request("GET", "/admin/skill/0");
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");

        // Not found
        $client->request("GET", "/admin/skill/99");
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
    }

    /**
     * Test delete action for a skill with a linked resume in fr.
     *
     * @return void
     */
    public function testDeleteActionWithLinkedResumeInFr(): void {
        try {
            // Prepare datas
            $this->prepareDatas();

            // Login as admin
            $crawler = $this->logInAdmin();
            $client = $this->getClient();

            // Click on nav-admin button
            $crawler = $client->click($crawler->selectLink("Gestion des compétences")->link());
            $this->assertEquals(1, $crawler->filter("#skills-container > table > tbody > tr")->count(), "1. The table should contain 1 row");
            $crawler = $client->click($crawler->selectLink('Voir les détails')->link());
            $this->assertRegExp("~^/admin/skill/[0-9]+/show~", $client->getRequest()->getRequestUri(), '2. The request uri expected is not ok.');

            // Try to delete the entity
            $client->submit($crawler->selectButton('Supprimer')->form());
            $crawler = $client->followRedirect();
            $this->assertRegExp("~^/admin/skill/[0-9]+/show~", $client->getRequest()->getRequestUri(), '3. The request uri expected is not ok.');
            $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-danger")->count(), "4. Error flash messages has to be displayed.");
            $this->assertRegExp("~La compétence n°[0-9]+ est associée à un ou plusieurs CV !~", $crawler->filter(".message-container")->text(), "5. The [delete] error flash message expected is not ok.");
        } catch (\Exception $exc) {
            $this->fail("0. Unexpected exception: " . $exc->getMessage());
        } finally {
            $this->cleanDatas([ResumeSkill::class, Skill::class, SkillLevel::class]);
        }
    }

    /**
     * Test delete action for a skill with a linked resume in en.
     *
     * @return void
     */
    public function testDeleteActionWithLinkedResumeInEn(): void {
        try {
            // Prepare datas
            $this->prepareDatas();

            // Login as admin
            $crawler = $this->logInAdmin("en");
            $client = $this->getClient();

            // Click on nav-admin button
            $crawler = $client->click($crawler->selectLink("Manage Skills")->link());
            $this->assertEquals(1, $crawler->filter("#skills-container > table > tbody > tr")->count(), "1. The table should contain 1 row");
            $crawler = $client->click($crawler->selectLink('Show details')->link());
            $this->assertRegExp("~^/en/admin/skill/[0-9]+/show~", $client->getRequest()->getRequestUri(), '2. The request uri expected is not ok.');

            // Try to delete the entity
            $client->submit($crawler->selectButton('Delete')->form());
            $crawler = $client->followRedirect();
            $this->assertRegExp("~^/en/admin/skill/[0-9]+/show~", $client->getRequest()->getRequestUri(), '3. The request uri expected is not ok.');
            $this->assertRegExp("~The skill No. [0-9]+ is linked to one or many resumes!~", $crawler->filter(".message-container")->text(), "4. The [delete] error flash message expected is not ok.");
        } catch (\Exception $exc) {
            $this->fail("0. Unexpected exception: " . $exc->getMessage());
        } finally {
            $this->cleanDatas([ResumeSkill::class, Skill::class, SkillLevel::class]);
        }
    }

    /**
     * Test complete scenario in fr.
     *
     * @return void
     */
    public function testCompleteScenarioInFr(): void {
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Gestion des compétences")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
        $this->assertEquals("/admin/skill", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Liste des compétences", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
        $this->assertEquals("Liste des compétences", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#skill-add-new")->html(), "1.6 The add button has not the picto expected.");
        $this->assertContains("Ajouter une nouvelle compétence", $crawler->filter("#skill-add-new")->text(), "1.7 The add button has not the content expected.");
        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skills-container")->text(), "1.8 The container should have the no-item message");

        // Clic on add new skill button
        $crawler = $client->click($crawler->selectLink("Ajouter une nouvelle compétence")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/admin/skill/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertEquals(2, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "2.3 Two actions buttons are expected.");
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > form > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsRow, "2.4 The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $actionsRow, "2.5 The return label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $actionsRow, "2.6 The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $actionsRow, "2.7 The save label expected is not ok");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_resumebundle_skill[title]' => 'Test'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertRegExp("~^/admin/skill/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '3.1 The request uri expected is not ok.');
        $this->assertContains("Test", $crawler->filter('#admin-container')->text(), '3.2 Missing element "Test".');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "3.3 Success flash messages has to be displayed.");
        $this->assertContains("Compétence ajoutée avec succès !", $crawler->filter(".message-container")->text(), "3.4 The [add] flash message expected is not ok.");
        $this->assertEquals(3, $crawler->filter("#admin-container > .jumbotron > .actions")->children()->count(), "3.5 Three actions buttons are expected.");
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsRow, "3.6 The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $actionsRow, "3.7 The return label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $actionsRow, "3.8 The edit picto expected is not in the actions div");
        $this->assertContains('Modifier', $actionsRow, "3.9 The edit label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsRow, "3.10 The delete picto expected is not in the actions div");
        $this->assertContains('Supprimer', $actionsRow, "3.11 The delete label expected is not ok");

        // Return to the list
        $crawler = $client->click($crawler->selectLink('Retour à la liste')->link());
        $this->assertEquals("/admin/skill", $client->getRequest()->getRequestUri(), "4.1 The request uri expected is not ok.");
        $this->assertEquals(1, $crawler->filter("#skills-container > table > tbody > tr")->count(), "4.2 The table should contain 1 row");

        // Vérify the actions button and their position.
        $children = $crawler->filter("#skills-container > table > tbody > tr > td > .actions")->children();
        $this->assertCount(3, $children, "4.3 Three actions buttons are expected");
        $this->assertContains('<i class="far fa-eye"></i>', $children->eq(1)->html(), "4.4 The see detail picto expected is not in the actions div");
        $this->assertContains('Voir les détail', $children->eq(1)->text(), "4.5 The return label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $children->eq(0)->html(), "4.6 The edit picto expected is not in the actions div");
        $this->assertContains('Modifier', $children->eq(0)->text(), "4.7 The edit label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $children->eq(2)->html(), "4.8 The delete picto expected is not in the actions div");
        $this->assertContains('Supprimer', $children->eq(2)->text(), "4.9 The delete label expected is not ok");

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Modifier')->link());
        $this->assertRegExp("~^/admin/skill/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), '5.1 The request uri expected is not ok.');
        $this->assertEquals(3, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "5.2 Three actions buttons are expected.");
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > form > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsRow, "5.3. The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $actionsRow, "5.4. The return label expected is not ok");
        $this->assertContains('<i class="far fa-eye"></i>', $actionsRow, "5.5. The show  picto expected is not in the actions div");
        $this->assertContains('Voir les détails', $actionsRow, "5.6. The show label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $actionsRow, "5.7. The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $actionsRow, "5.8. The save label expected is not ok");

        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_resumebundle_skill[title]' => 'Foo'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check if the element contains an attribute with value equals "Foo"
        $this->assertRegExp('/Foo/', $client->getResponse()->getContent(), '6.1. Missing element "Foo"');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "6.2 Success flash messages has to be displayed.");
        $this->assertRegExp("~Compétence n°[0-9]+ modifiée avec succès !~", $crawler->filter(".message-container")->text(), "6.3 The [edit] flash message expected is not ok.");

        // Delete the entity
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        // Check if the entity has been deleted on the list
        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skills-container")->text(), "7.1 The container should have the no-item message");
        $this->assertEquals("/admin/skill", $client->getRequest()->getRequestUri(), "7.2 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "7.3 Success flash messages has to be displayed.");
        $this->assertRegExp("~Compétence n°[0-9]+ supprimée avec succès !~", $crawler->filter(".message-container")->text(), "7.4 The [delete] flash message expected is not ok.");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioInEn(): void {
        $crawler = $this->logInAdmin("en");
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Manage Skills")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
        $this->assertEquals("/en/admin/skill", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Skills list", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
        $this->assertEquals("Skills list", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
        $this->assertContains("Add a new skill", $crawler->filter("#skill-add-new")->text(), "1.6 The add button has not the content expected.");
        $this->assertContains("There is no item for now! Please add one clicking on the button above!",
                $crawler->filter("#skills-container")->text(), "1.8 The container should have the no-item message");

        // Clic on add new skill button
        $crawler = $client->click($crawler->selectLink("Add a new skill")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/en/admin/skill/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > form > .actions")->text();
        $this->assertContains('Return to the list', $actionsRow, "2.3 The return label expected is not ok");
        $this->assertContains('Save', $actionsRow, "2.4 The save label expected is not ok");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Save')->form(array(
            'com_nairus_resumebundle_skill[title]' => 'Test'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertRegExp("~^/en/admin/skill/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '3.1 The request uri expected is not ok.');
        $this->assertContains("Test", $crawler->filter('#admin-container')->text(), '3.2 Missing element "Test".');
        $this->assertContains("Skill added successfully!", $crawler->filter(".message-container")->text(), "3.3 The [add] flash message expected is not ok.");
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > .actions")->text();
        $this->assertContains('Return to the list', $actionsRow, "3.4 The return label expected is not ok");
        $this->assertContains('Edit', $actionsRow, "3.5 The edit label expected is not ok");
        $this->assertContains('Delete', $actionsRow, "3.6 The delete label expected is not ok");

        // Return to the list
        $crawler = $client->click($crawler->selectLink('Return to the list')->link());
        $this->assertEquals("/en/admin/skill", $client->getRequest()->getRequestUri(), "4.1 The request uri expected is not ok.");
        $this->assertEquals(1, $crawler->filter("#skills-container > table > tbody > tr")->count(), "4.2 The table should contain 1 row");

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertRegExp("~^/en/admin/skill/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), '5.1 The request uri expected is not ok.');
        $actionsRow = $crawler->filter("#admin-container > .jumbotron > form > .actions")->text();
        $this->assertContains('Return to the list', $actionsRow, "5.2. The return label expected is not ok");
        $this->assertContains('Show details', $actionsRow, "5.3. The show label expected is not ok");
        $this->assertContains('Save', $actionsRow, "5.4. The save label expected is not ok");

        $form = $crawler->selectButton('Save')->form(array(
            'com_nairus_resumebundle_skill[title]' => 'Foo'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check if the element contains an attribute with value equals "Foo"
        $this->assertRegExp('/Foo/', $client->getResponse()->getContent(), '6.1. Missing element "Foo"');
        $this->assertRegExp("~Skill No. [0-9]+ modified successfully!~", $crawler->filter(".message-container")->text(), "6.2 The [edit] flash message expected is not ok.");


        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check if the entity has been deleted on the list
        $this->assertContains("There is no item for now! Please add one clicking on the button above!",
                $crawler->filter("#skills-container")->text(), "7.1 The container should have the no-item message");
        $this->assertEquals("/en/admin/skill", $client->getRequest()->getRequestUri(), "7.2 The request uri expected is not ok.");
        $this->assertRegExp("~Skill No. [0-9]+ deleted successfully!~", $crawler->filter(".message-container")->text(), "7.3 The [delete] flash message expected is not ok.");
    }

    /**
     * Test delete from index action.
     *
     * @return void
     */
    public function testDeleteFromIndexAction(): void {
        // Load datas
        $loadSkill = new LoadSkill();
        $loadSkill->load($this->getEntityManager());

        // Login and go to the index page.
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $crawler = $client->click($crawler->selectLink("Gestion des compétences")->link());

        $this->assertEquals(2, $crawler->filter("#skills-container > table > tbody > tr")->count(), "1. The table should contain 2 rows");

        // Delete the first entity.
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertEquals(1, $crawler->filter("#skills-container > table > tbody > tr")->count(), "2. The table should contain 1 row");

        // Delete the second entity.
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skills-container")->text(), "3. The container should have the no-item message");
    }

    /**
     * Test form validation.
     *
     * @return void
     */
    public function testFormValidation(): void {
        // Login and go to the new page.
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $crawler = $client->click($crawler->selectLink("Gestion des compétences")->link());
        $crawler = $client->click($crawler->selectLink("Ajouter une nouvelle compétence")->link());

        // Fill in the form.
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_resumebundle_skill[title]' => ' '
        ));

        // Submit the form
        $crawler = $client->submit($form);

        // Verify if there are some errors
        $this->assertCount(1, $crawler->filter(".is-invalid"), "1.1 The form has to show 2 inputs in error.");
        $this->assertCount(1, $crawler->filter(".invalid-feedback"), "1.2 The form has to show 2 errors messages.");
    }

    /**
     * Prepare data for tests.
     *
     * @return void
     */
    private function prepareDatas(): void {
        // Create new skill
        $skill = new Skill();
        $skill->setTitle("Symfony 3");
        $skillLevel = new SkillLevel();

        // Link it to a resume
        $entityManager = $this->getEntityManager();
        $resume = $entityManager->find(NSResumeBundle::NAME . ":Resume", 1);
        $resumeSkill = new ResumeSkill();
        $resumeSkill->setRank(1)
                ->setResume($resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);

        $entityManager->persist($skillLevel);
        $entityManager->persist($skill);
        $entityManager->persist($resumeSkill);
        $entityManager->flush();
    }

}
