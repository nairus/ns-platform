<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;

/**
 * Skill controller tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillControllerTest extends AbstractUserWebTestCase {

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
     *
     * @return void
     */
    public function testDeleteSkillWithLinkedResumeInFr(): void {
        $this->markTestIncomplete("TODO");
    }

    /**
     *
     * @return void
     */
    public function testDeleteSkillWithLinkedResumeInEn(): void {
        $this->markTestIncomplete("TODO");
    }

    /**
     * Test complete scenario.
     *
     * @return void
     */
    public function testCompleteScenarioInFr(): void {
        $this->markTestIncomplete("TODO");
//        $this->logInAdmin();
//        $client = $this->getClient();
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The status code expected is not ok.");
//
//        // Click on nav-admin button
//        $crawler = $client->click($crawler->selectLink("Gestion des compétences")->link());
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.2 The status code expected is not ok.");
//        $this->assertEquals("/admin/skill", $client->getRequest()->getRequestUri(), "1.3 The request uri expected is not ok.");
//        $this->assertEquals("Liste des compétences", $crawler->filter("html > head > title")->text(), "1.4 The title page expected is not ok.");
//        $this->assertEquals("Liste des compétences", $crawler->filter("h1")->text(), "1.5 The h1 title expected is not ok.");
//        $this->asserEquals('<i class="fas fa-plus"></i>', $crawler->filter("#skill-add-new")->html(), "1.6 The add button has not the picto expected.");
//        $this->asserEquals("Ajouter une nouvelle compétence", $crawler->filter("#skill-add-new")->text(), "1.7 The add button has not the content expected.");
//        $this->assertEquals("Il n'y a aucune donnée pour le moment ! S'il vous plait ajouter en une en cliquant sur le bouton ci-dessous !",
//                $crawler->filter("#skills-container")->text(), "1.8 The container should have the no-item message");
//
//        // Clic on add new skill button
//        $crawler = $client->click($crawler->selectLink("Ajouter une nouvelle compétence")->link());
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 The status code expected is not ok.");
//        $this->assertEquals("/admin/skill/new", $client->getRequest()->getRequestUri(), "2.2 The request uri expected is not ok.");
//
//        // Fill in the form and submit it
//        $form = $crawler->selectButton('Create')->form(array(
//            'com_nairus_resumebundle_skill[title]' => 'Test'
//        ));
//
//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//        // Check data in the show view
//        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), '2.3 Missing element td:contains("Test")');
//
//        // Edit the entity
//        $crawler = $client->click($crawler->selectLink('Edit')->link());
//
//        $form = $crawler->selectButton('Update')->form(array(
//            'com_nairus_resumebundle_skill[title]' => 'Foo'
//        ));
//
//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//        // Check the element contains an attribute with value equals "Foo"
//        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
//
//        // Return to the list
//        //$this->assertEquals(2, $crawler->filter("#skill-container > table > tbody > tr")->count(), "2.2 The table should contain 2 lignes");
//        // Delete the entity
//        $client->submit($crawler->selectButton('Delete')->form());
//        $crawler = $client->followRedirect();
//
//        // Check the entity has been delete on the list
//        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    public function testCompleteScenarioInEn(): void {
        $this->markTestIncomplete("TODO");
    }

}
