<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;

/**
 * Functional tests for SkillLevel controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelControllerTest extends AbstractUserWebTestCase {

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
        $this->markTestIncomplete("TODO");
    }

    /**
     * Test delete action for a skill with a linked resume in en.
     *
     * @return void
     */
    public function testDeleteActionWithLinkedResumeInEn(): void {
        $this->markTestIncomplete("TODO");
    }

    /**
     * Test complete scenario in fr.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        $this->markTestIncomplete("TODO");
    }

    /**
     * Test complete scenario in en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        $this->markTestIncomplete("TODO");
    }

}
