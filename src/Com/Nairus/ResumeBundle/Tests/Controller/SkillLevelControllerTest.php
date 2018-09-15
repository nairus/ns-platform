<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

/**
 * Functional tests for SkillLevel controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelControllerTest extends AbstractUserWebTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * Test index action with bad credentials.
     *
     * @covers \Com\Nairus\ResumeBundle\Controller\SkillLevelController::indexAction
     * @covers \Com\Nairus\CoreBundle\Controller\ErrorController::showAction
     *
     * @return void
     */
    public function testIndexActionWithBadCredentials(): void {
        $this->logInModerator();
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The status code expected is not ok.");

        // Try to reach skilllevel index action.
        $client->request("GET", "/admin/skilllevel/");
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
        $client->click($crawler->selectLink("Gestion des niveaux de compétence")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
        $this->assertEquals("/admin/skilllevel/", $client->getRequest()->getRequestUri(), "3. The request uri expected is not ok.");
    }

    /**
     * Test delete action for a skill with a linked resume in fr.
     *
     * @return void
     */
    public function testDeleteActionWithLinkedResumeInFr(): void {
        try {
            $this->prepareDatas();

            // Login as admin
            $crawler = $this->logInAdmin();
            $client = $this->getClient();


            // Click on nav-admin button
            $crawler = $client->click($crawler->selectLink("Gestion des niveaux de compétence")->link());
            $this->assertEquals(1, $crawler->filter("#skilllevel-container > .row > div")->count(), "1. The page has to contain 1 item.");

            // Try to delete the entity
            $client->submit($crawler->selectButton('Supprimer')->form());
            $crawler = $client->followRedirect();
            $this->assertRegExp("~^/admin/skilllevel~", $client->getRequest()->getRequestUri(), '2. The request uri expected is not ok.');
            $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-danger")->count(), "3. Error flash messages has to be displayed.");
            $this->assertRegExp("~Le niveau de compétence n°[0-9]+ est associé à un ou plusieurs CV !~", $crawler->filter(".message-container")->text(),
                    "4. The [delete] error flash message expected is not ok.");
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
            $this->prepareDatas();

            // Login as admin
            $crawler = $this->logInAdmin("en");
            $client = $this->getClient();


            // Click on nav-admin button
            $crawler = $client->click($crawler->selectLink("Manage Skill levels")->link());
            $this->assertEquals(1, $crawler->filter("#skilllevel-container > .row > div")->count(), "1. The page has to contain 1 item.");

            // Try to delete the entity
            $client->submit($crawler->selectButton('Delete')->form());
            $crawler = $client->followRedirect();
            $this->assertRegExp("~^/en/admin/skilllevel~", $client->getRequest()->getRequestUri(), '2. The request uri expected is not ok.');
            $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-danger")->count(), "3. Error flash messages has to be displayed.");
            $this->assertRegExp("~The skill level No. [0-9]+ is linked to one or many resumes!~", $crawler->filter(".message-container")->text(),
                    "4. The [delete] error flash message expected is not ok.");
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
    public function testCompleteScenarioFr(): void {
        // Login as admin
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Gestion des niveaux de compétence")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
        $this->assertEquals("/admin/skilllevel/", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Liste des niveaux de compétence", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
        $this->assertEquals("Liste des niveaux de compétence", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
        $this->assertContains('<i class="fas fa-plus"></i>', $crawler->filter("#skilllevel-add-new")->html(), "1.6 The add button has not the picto expected.");
        $this->assertContains("Ajouter un nouveau niveau", $crawler->filter("#skilllevel-add-new")->text(), "1.7 The add button has not the content expected.");
        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skilllevel-container")->text(), "1.8 The page has to contain the no-item message.");

        // Clic on add new skill button
        $crawler = $client->click($crawler->selectLink("Ajouter un nouveau niveau")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/admin/skilllevel/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertEquals(2, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "2.3 Two actions buttons are expected.");
        $divElement = $crawler->filter("#admin-container > .jumbotron > form > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $divElement, "2.4 The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $divElement, "2.5 The return label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $divElement, "2.6 The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $divElement, "2.7 The save label expected is not ok");
        $this->assertContains("Ajout d'un niveau de compétence", $crawler->filter("html > head > title")->text(), "2.8 The title page expected is not ok.");
        $this->assertEquals("Ajout d'un niveau de compétence", $crawler->filter("h1")->text(), "2.9 The h1 title expected is not ok.");
        $this->assertEquals("Traductions", $crawler->filter("#com_nairus_resumebundle_skilllevel > fieldset > legend")->text(), "2.10 The legend label expected is not ok.");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_resumebundle_skilllevel[translations][fr][title]' => 'Test FR',
            'com_nairus_resumebundle_skilllevel[translations][en][title]' => 'Test EN',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the index view
        $this->assertEquals("/admin/skilllevel/", $client->getRequest()->getRequestUri(), '3.1 The request uri expected is not ok.');
        $this->assertContains("Test FR", $crawler->filter('#admin-container')->text(), '3.2 Missing element "Test FR".');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "3.3 Success flash messages has to be displayed.");
        $this->assertContains("Le niveau de compétence a été ajouté avec succès !", $crawler->filter(".message-container")->text(),
                "3.4 The [add] flash message expected is not ok.");
        $this->assertEquals(3, $crawler->filter(".actions")->count(), "3.5 Three actions buttons are expected.");
        $divElement = $crawler->filter("#skilllevel-container > .row > div")->html();
        $this->assertContains('<i class="far fa-eye"></i>', $divElement, "3.6 The see details picto expected is not in the actions div");
        $this->assertContains('Voir les détails', $divElement, "3.7 The see label label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $divElement, "3.8 The edit picto expected is not in the actions div");
        $this->assertContains('Modifier', $divElement, "3.9 The edit label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $divElement, "3.10 The delete picto expected is not in the actions div");
        $this->assertContains('Supprimer', $divElement, "3.11 The delete label expected is not ok");

        // Go to show action
        $crawler = $client->click($crawler->selectLink('Voir les détails')->link());
        $this->assertRegExp("~^/admin/skilllevel/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '4.1 The request uri expected is not ok.');
        $this->assertEquals(2, $crawler->filter("#skilllevel-content")->children()->count(), "4.2 The container should contain 2 elements");

        // Vérify the actions button and their position.
        $children = $crawler->filter("#admin-container > .jumbotron > .actions")->children();
        $this->assertCount(3, $children, "4.3 Three actions buttons are expected");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $children->eq(0)->html(), "4.4 The see detail picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $children->eq(0)->text(), "4.5 The return label expected is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $children->eq(1)->html(), "4.6 The edit picto expected is not in the actions div");
        $this->assertContains('Modifier', $children->eq(1)->text(), "4.7 The edit label expected is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $children->eq(2)->html(), "4.8 The delete picto expected is not in the actions div");
        $this->assertContains('Supprimer', $children->eq(2)->text(), "4.9 The delete label expected is not ok");

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Modifier')->link());
        $this->assertRegExp("~^/admin/skilllevel/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), '5.1 The request uri expected is not ok.');
        $this->assertEquals(3, $crawler->filter("#admin-container > .jumbotron > form > .actions")->children()->count(), "5.2 Three actions buttons are expected.");
        $divElement = $crawler->filter("#admin-container > .jumbotron > form > .actions")->html();
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $divElement, "5.3. The return picto expected is not in the actions div");
        $this->assertContains('Retour à la liste', $divElement, "5.4. The return label expected is not ok");
        $this->assertContains('<i class="far fa-eye"></i>', $divElement, "5.5. The show  picto expected is not in the actions div");
        $this->assertContains('Voir les détails', $divElement, "5.6. The show label expected is not ok");
        $this->assertContains('<i class="far fa-save"></i>', $divElement, "5.7. The save picto expected is not in the actions div");
        $this->assertContains('Sauvegarder', $divElement, "5.8. The save label expected is not ok");

        $form = $crawler->selectButton('Sauvegarder')->form(array(
            'com_nairus_resumebundle_skilllevel[translations][fr][title]' => 'Foo FR',
            'com_nairus_resumebundle_skilllevel[translations][en][title]' => 'Test EN',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertRegExp('/Foo FR/', $client->getResponse()->getContent(), '6.1. Missing element "Foo FR"');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "6.2 Success flash messages has to be displayed.");
        $this->assertRegExp("~Le niveau de compétence n°[0-9]+ a été modifié avec succès !~", $crawler->filter(".message-container")->text(),
                "6.3 The [edit] flash message expected is not ok.");
        $this->assertEquals("/admin/skilllevel/", $client->getRequest()->getRequestUri(), "6.4 The request uri expected is not ok.");

        // Delete the entity
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        // Check if the entity has been deleted on the list
        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skilllevel-container")->text(), "7.1 The container should have the no-item message");
        $this->assertEquals("/admin/skilllevel/", $client->getRequest()->getRequestUri(), "7.2 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "7.3 Success flash messages has to be displayed.");
        $this->assertRegExp("~Le niveau de compétence n°[0-9]+ a été supprimé avec succès !~", $crawler->filter(".message-container")->text(),
                "7.4 The [delete] flash message expected is not ok.");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        // Login as admin
        $crawler = $this->logInAdmin("en");
        $client = $this->getClient();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");

        // Click on nav-admin button
        $crawler = $client->click($crawler->selectLink("Manage Skill levels")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
        $this->assertEquals("/en/admin/skilllevel/", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
        $this->assertContains("Skill levels list", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
        $this->assertEquals("Skill levels list", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
        $this->assertContains("Add a new level", $crawler->filter("#skilllevel-add-new")->text(), "1.6 The add button has not the content expected.");
        $this->assertContains("There is no item for now! Please add one clicking on the button above!", $crawler->filter("#skilllevel-container")->text(),
                "1.7 The page has to contain the no-item message.");

        // Clic on add new skill button
        $crawler = $client->click($crawler->selectLink("Add a new level")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
        $this->assertEquals("/en/admin/skilllevel/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
        $this->assertContains("Add a Skill level", $crawler->filter("html > head > title")->text(), "2.3 The title page expected is not ok.");
        $this->assertEquals("Add a Skill level", $crawler->filter("h1")->text(), "2.4 The h1 title expected is not ok.");
        $this->assertEquals("Translations", $crawler->filter("#com_nairus_resumebundle_skilllevel > fieldset > legend")->text(), "2.5 The legend label expected is not ok.");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Save')->form(array(
            'com_nairus_resumebundle_skilllevel[translations][fr][title]' => 'Test FR',
            'com_nairus_resumebundle_skilllevel[translations][en][title]' => 'Test EN',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the index view
        $this->assertEquals("/en/admin/skilllevel/", $client->getRequest()->getRequestUri(), '3.1 The request uri expected is not ok.');
        $this->assertContains("Test EN", $crawler->filter('#admin-container')->text(), '3.2 Missing element "Test EN".');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "3.3 Success flash messages has to be displayed.");
        $this->assertContains("Skill level added successfully!", $crawler->filter(".message-container")->text(),
                "3.4 The [add] flash message expected is not ok.");
        // Go to show action
        $crawler = $client->click($crawler->selectLink('Show details')->link());
        $this->assertRegExp("~^/en/admin/skilllevel/[0-9]+/show$~", $client->getRequest()->getRequestUri(), '4.1 The request uri expected is not ok.');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertRegExp("~^/en/admin/skilllevel/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), '5.1 The request uri expected is not ok.');

        $form = $crawler->selectButton('Save')->form(array(
            'com_nairus_resumebundle_skilllevel[translations][fr][title]' => 'Test FR',
            'com_nairus_resumebundle_skilllevel[translations][en][title]' => 'Foo EN',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertRegExp('/Foo EN/', $client->getResponse()->getContent(), '6.1. Missing element "Foo EN"');
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "6.2 Success flash messages has to be displayed.");
        $this->assertRegExp("~Skill level No. [0-9]+ modified successfully!~", $crawler->filter(".message-container")->text(), "6.3 The [edit] flash message expected is not ok.");
        $this->assertEquals("/en/admin/skilllevel/", $client->getRequest()->getRequestUri(), "6.4 The request uri expected is not ok.");

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check if the entity has been deleted on the list
        $this->assertContains("There is no item for now! Please add one clicking on the button above!",
                $crawler->filter("#skilllevel-container")->text(), "7.1 The container should have the no-item message");
        $this->assertEquals("/en/admin/skilllevel/", $client->getRequest()->getRequestUri(), "7.2 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter(".message-container > .alert-success")->count(), "7.3 Success flash messages has to be displayed.");
        $this->assertRegExp("~Skill level No. [0-9]+ deleted successfully!~", $crawler->filter(".message-container")->text(), "7.4 The [delete] flash message expected is not ok.");
    }

    /**
     * Test delete from index action.
     *
     * @return void
     */
    public function testDeleteFromIndexAction(): void {
        // Load datas
        $skillLevel = new SkillLevel();
        $skillLevel
                ->setCurrentLocale("fr")
                ->translate()->setTitle('Test');
        $this->loadDatas($this->getEntityManager(), [$skillLevel]);

        // Login and go to the index page.
        $crawler = $this->logInAdmin();
        $client = $this->getClient();
        $crawler = $client->click($crawler->selectLink("Gestion des niveaux de compétence")->link());

        $this->assertEquals(1, $crawler->filter("#skilllevel-container > .row > div")->count(), "1. The page has to contain 1 item.");

        // Delete the entity.
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();

        $this->assertContains("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
                $crawler->filter("#skilllevel-container")->text(), "2. The container should have the no-item message");
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
        $crawler = $client->click($crawler->selectLink("Gestion des niveaux de compétence")->link());
        $crawler = $client->click($crawler->selectLink("Ajouter un nouveau niveau")->link());

        // Fill in the form.
        $form = $crawler->selectButton('Sauvegarder')->form([
            'com_nairus_resumebundle_skilllevel[translations][en][title]' => 'Bad',
        ]);

        // Submit the form
        $crawler = $client->submit($form);

        // Verify if there are some errors
        $this->assertCount(2, $crawler->filter(".is-invalid"), "1.1 The form has to show 2 inputs in error.");
        $this->assertCount(2, $crawler->filter(".invalid-feedback"), "1.2 The form has to show 2 errors messages.");
        $this->assertContains("Il y a des erreurs dans le formulaire ! Regardez dans les champs de traductions non visibles !",
                $crawler->filter("#global-errors-container")->text(), "1.3 The global errors message is missing.");
    }

    /**
     * Prepare Datas for tests.
     *
     * @return void
     */
    private function prepareDatas(): void {
        $entityManager = $this->getEntityManager();

        // Load main datas
        $this->loadDatas($entityManager, [new LoadSkill(), new SkillLevel()]);

        // Get the entities to link.
        $skill = $entityManager->getRepository(NSResumeBundle::NAME . ":Skill")->findAll()[0];
        $skillLevel = $entityManager->getRepository(NSResumeBundle::NAME . ":SkillLevel")->findAll()[0];

        // Get the resume in the global fixtures.
        $resume = $entityManager->find(NSResumeBundle::NAME . ":Resume", 1);

        // Create a resume skill.
        $resumeSkill = new ResumeSkill();
        $resumeSkill
                ->setRank(1)
                ->setResume($resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);
        $entityManager->persist($resumeSkill);
        $entityManager->flush();
    }

}
