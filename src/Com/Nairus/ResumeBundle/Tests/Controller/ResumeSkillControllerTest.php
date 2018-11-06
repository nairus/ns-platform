<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeOnline;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * ResumeSkillController functional tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillControllerTest extends AbstractUserWebTestCase {

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

        // clean admin resume datas
        $author = $this->getEntityManager()->getRepository(\Com\Nairus\UserBundle\Entity\User::class)->findByUsername("author");
        $resumes = $this->getEntityManager()->getRepository(Resume::class)->findByAuthor($author);
        foreach ($resumes as /* @var $resume Resume */ $resume) {
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $resume->removeResumeSkill($resumeSkill);
                $this->getEntityManager()->remove($resumeSkill);
            }
        }
        $this->getEntityManager()->flush();

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
        $client->request(Request::METHOD_GET, "/restricted/resumeskill/$resumeId/new");
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

        $client->request(Request::METHOD_GET, "/restricted/resumeskill/999999/new");
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
        /* @var $resumeSkill ResumeSkill */
        $resumeSkill = $this->resume->getResumeSkills()->first();
        $id = $resumeSkill->getId();
        $client->request(Request::METHOD_GET, "/restricted/resumeskill/$id/edit");
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
        /* @var $resumeSkill ResumeSkill */
        $resumeSkill = $this->resume->getResumeSkills()->first();
        $id = $resumeSkill->getId();
        $client->request(Request::METHOD_GET, "/restricted/resumeskill/$id/show");
        // This page doesn't exist.
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");
    }

    /**
     * Test delete action with bad credential.
     *
     * @return void
     */
    public function testDeleteActionWithBadCredential(): void {
        $this->logInAdmin();
        $client = $this->getClient();
        /* @var $resumeSkill ResumeSkill */
        $resumeSkill = $this->resume->getResumeSkills()->first();
        $id = $resumeSkill->getId();
        $client->request(Request::METHOD_DELETE, "/restricted/resumeskill/$id/delete");
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

        // Click on the add resume skill button.
        $crawler = $client->click($crawler->selectLink("Ajouter une compétence")->link());

        // Case 1: Add a new resume skill
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resumeskill/[0-9]+/new$~", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertRegExp("~Ajout d'une compétence pour le CV n°[0-9]+~", $crawler->filter("html > head > title")->text(), "1.3 The page title exepected is not ok");
        $this->assertRegExp("~Ajout d'une compétence pour le CV n°[0-9]+~", $crawler->filter("h1")->text(), "1.4 The h1 expected is not ok");

        // Get the form
        $newForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="com_nairus_resumebundle_resumeskill"]');
        $this->assertEquals(3, $newForm->filter(".form-group")->count(), "1.4 Three form elements are expected.");
        $this->assertContains("Classement", $newForm->text(), "1.5 The form has to contain rank label.");
        $this->assertContains("Compétence", $newForm->text(), "1.6 The form has to contain skill label.");
        $this->assertContains("Niveau", $newForm->text(), "1.7 The form has to contain skill level label.");
        $actionsElements = $newForm->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.8 Two cta buttons are expected.");
        $this->assertContains("Retour au CV", $actionsElements->eq(0)->text(), "1.9 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "1.10 The first button pico is missing.");
        $this->assertContains("Sauvegarder", $actionsElements->eq(1)->text(), "1.11 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "1.12 The second button pico is missing.");

        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[0];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[0];
        // Get the titles to test form submission.
        $skillTitle = $skill->getTitle();
        $skillLevelTitle = $skillLevel->setCurrentLocale("fr")->getTitle();

        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 1,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 2: Return on resume show page
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "2.1 The request should redirect on resume show page.");
        $this->assertContains("Compétence de CV ajoutée avec succès !", $crawler->filter(".message-container .alert-success")->text(),
                "2.2 The success flash message should be displayed.");
        $this->assertEquals(1, $crawler->filter("#skills-content .card")->count(), "2.3 One resume skill card is expected.");
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $this->assertContains("Modifier", $cardFooterElements->eq(0)->text(), "2.4 The edit label is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $cardFooterElements->eq(0)->html(), "2.5 The edit picto is not ok");
        $this->assertContains("Supprimer", $cardFooterElements->eq(1)->text(), "2.6 The delete label is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $cardFooterElements->eq(1)->html(), "2.7 The delete picto is not ok");
        $cardBody = $crawler->filter("#skills-content .card")->first()->filter(".card-body")->text();
        $this->assertContains("Classement : 1", $cardBody, "2.8 The rank label is not ok");
        $this->assertContains($skillTitle, $cardBody, "2.9 The skill title is not ok");
        $this->assertContains($skillLevelTitle, $cardBody, "2.10 The skill level title is not ok");

        // Case 3: Edit the resume skill
        $crawler = $client->click($cardFooterElements->selectLink("Modifier")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.1 The status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resumeskill/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertRegExp("~Modification de la compétence n°[0-9]+ du CV n°[0-9]+~", $crawler->filter("html > head > title")->text(), "3.3 The page title exepected is not ok");
        $this->assertRegExp("~Modification de la compétence n°[0-9]+ du CV n°[0-9]+~", $crawler->filter("h1")->text(), "3.4 The h1 expected is not ok");

        // Get the form
        $editForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="com_nairus_resumebundle_resumeskill"]');
        $this->assertEquals(3, $editForm->filter(".form-group")->count(), "3.4 Three form elements are expected.");
        $this->assertContains("Classement", $editForm->text(), "3.5 The form has to contain rank label.");
        $this->assertContains("Compétence", $editForm->text(), "3.6 The form has to contain skill label.");
        $this->assertContains("Niveau", $editForm->text(), "3.7 The form has to contain skill level label.");
        $actionsElements = $editForm->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "3.8 Two cta buttons are expected.");
        $this->assertContains("Retour au CV", $actionsElements->eq(0)->text(), "3.9 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "3.10 The first button pico is missing.");
        $this->assertContains("Sauvegarder", $actionsElements->eq(1)->text(), "3.11 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "3.12 The second button pico is missing.");

        // Get the delete form
        $deleteForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="form"]');
        $this->assertContains("Supprimer", $deleteForm->filter("button")->text(), "3.13 The delete label is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $deleteForm->filter("button")->html(), "3.13 The delete picto is missing");

        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[1];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[1];
        // Get the titles to test form submission.
        $skillTitle = $skill->getTitle();
        $skillLevelTitle = $skillLevel->setCurrentLocale("fr")->getTitle();
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 2,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 4: Return to the resume show page.
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "4.1 The request should redirect on resume show page.");
        $this->assertRegExp("~Compétence de CV n°[0-9]+ modifiée avec succès !~", $crawler->filter(".message-container .alert-success")->text(),
                "4.2 The success flash message should be displayed.");
        $cardBody = $crawler->filter("#skills-content .card")->first()->filter(".card-body")->text();
        $this->assertContains("Classement : 2", $cardBody, "4.3 The rank label is not ok");
        $this->assertContains($skillTitle, $cardBody, "4.4 The skill title is not ok");
        $this->assertContains($skillLevelTitle, $cardBody, "4.5 The skill level title is not ok");

        // Case 5: Delete the resume skill
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Supprimer")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "5.1 The status code expected is not ok.");
        $this->assertRegExp("~^/restricted/resumeskill/[0-9]+/delete~", $client->getRequest()->getRequestUri(), "5.2 The request uri expected is not ok.");
        $this->assertRegExp("~Suppression de la compétence n°[0-9]+ du CV n°[0-9]+~", $crawler->filter("html > head > title")->text(),
                "5.3 The page title exepected is not ok");
        $this->assertRegExp("~Suppression de la compétence n°[0-9]+ du CV n°[0-9]+~", $crawler->filter("h1")->text(), "5.4 The h1 expected is not ok");
        $this->assertRegExp('~Êtes-vous sûr de vouloir supprimer la compétence ".+" au rang n°[0-9]+ ?~', $crawler->filter("#admin-container")->text(),
                "5.5 The confirm message is not ok");
        $actionsButtons = $crawler->filter("#admin-container .actions")->children();
        $this->assertEquals(2, $actionsButtons->count(), "5.6 Two cta buttons are expected.");
        $this->assertContains("Retour au CV", $actionsButtons->eq(0)->text(), "5.7 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsButtons->eq(0)->html(), "5.8 The first button pico is missing.");
        $this->assertContains("Supprimer", $actionsButtons->eq(1)->text(), "5.9 The second button label is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsButtons->eq(1)->html(), "5.10 The second button pico is missing.");

        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertRegExp("~^/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "5.11 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "5.12 The [delete] flash message is missing.");
        $this->assertRegExp("~Compétence de CV n°[0-9]+ supprimée avec succès !~", $crawler->filter('.message-container')->text(), "5.13 The [delete] flash message is not ok.");
        $this->assertContains("Il n'y a aucune donnée pour le moment !", $crawler->filter("#skills-content")->text(), "5.14 The no item message is expected.");
        $this->assertEquals(0, $crawler->filter("#skills-content .card")->count(), "5.15 No card block is expected.");
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

        // Click on the add resume skill button.
        $crawler = $client->click($crawler->selectLink("Add a skill")->link());

        // Case 1: Add a new resume skill
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resumeskill/[0-9]+/new$~", $client->getRequest()->getRequestUri(), "1.2 The request uri expected is not ok.");
        $this->assertRegExp("~Add a skill for the resume No. [0-9]+~", $crawler->filter("html > head > title")->text(), "1.3 The page title exepected is not ok");
        $this->assertRegExp("~Add a skill for the resume No. [0-9]+~", $crawler->filter("h1")->text(), "1.4 The h1 expected is not ok");

        // Get the form
        $newForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="com_nairus_resumebundle_resumeskill"]');
        $this->assertEquals(3, $newForm->filter(".form-group")->count(), "1.4 Three form elements are expected.");
        $this->assertContains("Rank", $newForm->text(), "1.5 The form has to contain rank label.");
        $this->assertContains("Skill", $newForm->text(), "1.6 The form has to contain skill label.");
        $this->assertContains("Level", $newForm->text(), "1.7 The form has to contain skill level label.");
        $actionsElements = $newForm->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "1.8 Two cta buttons are expected.");
        $this->assertContains("Return to the resume", $actionsElements->eq(0)->text(), "1.9 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "1.10 The first button pico is missing.");
        $this->assertContains("Save", $actionsElements->eq(1)->text(), "1.11 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "1.12 The second button pico is missing.");

        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[0];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[0];
        // Get the titles to test form submission.
        $skillTitle = $skill->getTitle();
        $skillLevelTitle = $skillLevel->setCurrentLocale("fr")->getTitle();

        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 1,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 2: Return on resume show page
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "2.1 The request should redirect on resume show page.");
        $this->assertContains("Resume skill added successfully!", $crawler->filter(".message-container .alert-success")->text(),
                "2.2 The success flash message should be displayed.");
        $this->assertEquals(1, $crawler->filter("#skills-content .card")->count(), "2.3 One resume skill card is expected.");
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $this->assertContains("Edit", $cardFooterElements->eq(0)->text(), "2.4 The edit label is not ok");
        $this->assertContains('<i class="fas fa-pencil-alt"></i>', $cardFooterElements->eq(0)->html(), "2.5 The edit picto is not ok");
        $this->assertContains("Delete", $cardFooterElements->eq(1)->text(), "2.6 The delete label is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $cardFooterElements->eq(1)->html(), "2.7 The delete picto is not ok");
        $cardBody = $crawler->filter("#skills-content .card")->first()->filter(".card-body")->text();
        $this->assertContains("Ranking: 1", $cardBody, "2.8 The rank label is not ok");
        $this->assertContains($skillTitle, $cardBody, "2.9 The skill title is not ok");
        $this->assertContains($skillLevelTitle, $cardBody, "2.10 The skill level title is not ok");

        // Case 3: Edit the resume skill
        $crawler = $client->click($cardFooterElements->selectLink("Edit")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "3.1 The status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resumeskill/[0-9]+/edit$~", $client->getRequest()->getRequestUri(), "3.2 The request uri expected is not ok.");
        $this->assertRegExp("~Modification of the skill No. [0-9]+ for the resume No. [0-9]+~", $crawler->filter("html > head > title")->text(),
                "3.3 The page title exepected is not ok");
        $this->assertRegExp("~Modification of the skill No. [0-9]+ for the resume No. [0-9]+~", $crawler->filter("h1")->text(),
                "3.4 The h1 expected is not ok");

        // Get the form
        $editForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="com_nairus_resumebundle_resumeskill"]');
        $this->assertEquals(3, $editForm->filter(".form-group")->count(), "3.4 Three form elements are expected.");
        $this->assertContains("Rank", $editForm->text(), "3.5 The form has to contain rank label.");
        $this->assertContains("Skill", $editForm->text(), "3.6 The form has to contain skill label.");
        $this->assertContains("Level", $editForm->text(), "3.7 The form has to contain skill level label.");
        $actionsElements = $editForm->filter(".actions")->children();
        $this->assertCount(2, $actionsElements, "3.8 Two cta buttons are expected.");
        $this->assertContains("Return to the resume", $actionsElements->eq(0)->text(), "3.9 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsElements->eq(0)->html(), "3.10 The first button pico is missing.");
        $this->assertContains("Save", $actionsElements->eq(1)->text(), "3.11 The second button label is not ok.");
        $this->assertContains('<i class="far fa-save"></i>', $actionsElements->eq(1)->html(), "3.12 The second button pico is missing.");

        // Get the delete form
        $deleteForm = $crawler->filterXPath('//html/body/main/div[@id="admin-container"]/div/form[@name="form"]');
        $this->assertContains("Delete", $deleteForm->filter("button")->text(), "3.13 The delete label is not ok");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $deleteForm->filter("button")->html(), "3.13 The delete picto is missing");

        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findAll()[1];
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[1];
        // Get the titles to test form submission.
        $skillTitle = $skill->getTitle();
        $skillLevelTitle = $skillLevel->setCurrentLocale("fr")->getTitle();
        $form = $crawler->selectButton('Save')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 2,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Case 4: Delete the resume skill
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show$~", $client->getRequest()->getRequestUri(), "4.1 The request should redirect on resume show page.");
        $this->assertRegExp("~Resume skill No. [0-9]+ modified successfully!~", $crawler->filter(".message-container .alert-success")->text(),
                "4.2 The success flash message should be displayed.");
        $cardBody = $crawler->filter("#skills-content .card")->first()->filter(".card-body")->text();
        $this->assertContains("Ranking: 2", $cardBody, "4.3 The rank label is not ok");
        $this->assertContains($skillTitle, $cardBody, "4.4 The skill title is not ok");
        $this->assertContains($skillLevelTitle, $cardBody, "4.5 The skill level title is not ok");

        // Case 5: Delete the resume skill
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Delete")->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "5.1 The status code expected is not ok.");
        $this->assertRegExp("~^/en/restricted/resumeskill/[0-9]+/delete~", $client->getRequest()->getRequestUri(), "5.2 The request uri expected is not ok.");
        $this->assertRegExp("~Deletion of the skill No. [0-9]+ for the resume No. [0-9]+~", $crawler->filter("html > head > title")->text(),
                "5.3 The page title exepected is not ok");
        $this->assertRegExp("~Deletion of the skill No. [0-9]+ for the resume No. [0-9]+~", $crawler->filter("h1")->text(), "5.4 The h1 expected is not ok");
        $this->assertRegExp('~Are you sure you want to remove the ".+" skill from the rank No. [0-9]+?~', $crawler->filter("#admin-container")->text(),
                "5.5 The confirm message is not ok");
        $actionsButtons = $crawler->filter("#admin-container .actions")->children();
        $this->assertEquals(2, $actionsButtons->count(), "5.6 Two cta buttons are expected.");
        $this->assertContains("Return to the resume", $actionsButtons->eq(0)->text(), "5.7 The first button label is not ok.");
        $this->assertContains('<i class="fas fa-chevron-left"></i>', $actionsButtons->eq(0)->html(), "5.8 The first button pico is missing.");
        $this->assertContains("Delete", $actionsButtons->eq(1)->text(), "5.9 The second button label is not ok.");
        $this->assertContains('<i class="fas fa-trash-alt"></i>', $actionsButtons->eq(1)->html(), "5.10 The second button pico is missing.");

        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();
        $this->assertRegExp("~^/en/restricted/resume/[0-9]+/show~", $client->getRequest()->getRequestUri(), "5.11 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "5.12 The [delete] flash message is missing.");
        $this->assertRegExp("~Resume skill No. [0-9]+ deleted successfully!~", $crawler->filter('.message-container')->text(), "5.13 The [delete] flash message is not ok.");
        $this->assertContains("There is no item for now!", $crawler->filter("#skills-content")->text(), "5.14 The no item message is expected.");
        $this->assertEquals(0, $crawler->filter("#skills-content .card")->count(), "5.15 No card block is expected.");
    }

    /**
     * Test delete from resume skill edit page.
     *
     * @return void
     */
    public function testDeleteFromEditPage(): void {
        $crawler = $this->logInModerator();
        $client = $this->getClient();

        // Go to resume show
        $resumeId = $this->resume->getId();
        $crawler = $client->request(Request::METHOD_GET, "/restricted/resume/$resumeId/show");

        // Go to resume skill edit page
        $cardFooterElements = $crawler->filter("#skills-content .card")->first()->filter(".card-footer")->children();
        $crawler = $client->click($cardFooterElements->selectLink("Modifier")->link());

        // Click on the delete button
        $client->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $client->followRedirect();
        $this->assertEquals("/restricted/resume/$resumeId/show", $client->getRequest()->getRequestUri(), "1.1 The request uri expected is not ok.");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "1.2 The [delete] flash message is missing.");
        $this->assertRegExp("~Compétence de CV n°[0-9]+ supprimée avec succès !~", $crawler->filter('.message-container')->text(), "1.3 The [delete] flash message is not ok.");
        $this->assertContains("Il n'y a aucune donnée pour le moment !", $crawler->filter("#skills-content")->text(), "1.4 The no item message is expected.");
        $this->assertEquals(0, $crawler->filter("#skills-content .card")->count(), "1.5 No card block is expected.");
    }

    /**
     * Test the validation of the form.
     *
     * @return void
     */
    public function testValidateForm(): void {
        $crawler = $this->logInAuthor();
        $client = $this->getClient();

        // Go the resume page
        $crawler = $client->click($crawler->selectLink("Mes CV")->link());

        // Go on the resume detail page
        $crawler = $client->click($crawler->selectLink("Voir les détails")->link());

        // Click on the add experience button.
        $crawler = $client->click($crawler->selectLink("Ajouter une compétence")->link());

        /* @var $skill Skill */
        $skill = $this->getEntityManager()->getRepository(Skill::class)->findOneByTitle("Python 2/3");
        /* @var $skillLevel SkillLevel */
        $skillLevel = $this->getEntityManager()->getRepository(SkillLevel::class)->findAll()[2];

        // Submit form
        $form = $crawler->selectButton('Sauvegarder')->form([
            "com_nairus_resumebundle_resumeskill[rank]" => 0,
            "com_nairus_resumebundle_resumeskill[skill]" => $skill->getId(),
            "com_nairus_resumebundle_resumeskill[skillLevel]" => $skillLevel->getId()
        ]);

        $crawler = $client->submit($form);

        $this->assertRegExp("~/restricted/resumeskill/[0-9]+/new~", $client->getRequest()->getRequestUri(), "1. The request uri expected is not ok.");

        // Verify if there are some errors
        $this->assertCount(1, $crawler->filter(".is-invalid"), "2.1 The form has to show 1 inputs in error.");
        $this->assertCount(1, $crawler->filter(".invalid-feedback"), "2.2 The form has to show 1 errors message.");
    }

}
