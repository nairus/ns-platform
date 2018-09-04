<?php

namespace Com\Nairus\UserBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;

/**
 * Test of ProfileController.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ProfileControllerTest extends AbstractUserWebTestCase {

    /**
     * Test the overriding of show action views in fr.
     *
     * @return void
     */
    public function testShowActionFr(): void {
        // Login as user.
        $crawler = $this->logInUser();
        $client = $this->getClient();

        // The router musts redirect to the profile show page.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The HTTP code expected is not ok.");
        $this->assertEquals("/account/profile/", $client->getRequest()->getRequestUri(), "1.2 The uri expected is not ok.");

        $this->assertContains("Détail de mon compte", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("Détail de mon compte", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");
        $this->assertContains('<div id="actions">', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `div` with `actions` css id.");
        $this->assertEquals(2, $crawler->filter("#actions a")->count(), "2.4 The page doesn't contain 2 buttons.");
        $this->assertContains('<i class="fas fa-pencil-alt"></i> Modifier', $crawler->filter("#user-profile-edit")->html(), "2.5 The edit button doesn't contain the good translation.");
        $this->assertContains('<i class="fas fa-user-shield"></i> Changer mon mot de passe', $crawler->filter("#user-change-password")->html(), "2.6 The change password button doesn't contain the good translation.");
    }

    /**
     * Test the overriding of show action views in en.
     *
     * @return void
     */
    public function testShowActionEn(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // Redirect to the english page in .
        $client->request("GET", "/en/account/profile");
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The HTTP code expected is not ok.");
        $this->assertContains("My account detail", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("My account detail", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");
        $this->assertContains('<div id="actions">', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `div` with `actions` css id.");
        $this->assertEquals(2, $crawler->filter("#actions a")->count(), "2.4 The page doesn't contain 2 buttons.");
        $this->assertContains("Edit", $crawler->filter("#user-profile-edit")->text(), "2.5 The edit button doesn't contain the good translation.");
        $this->assertContains("Change my password", $crawler->filter("#user-change-password")->text(), "2.6 The change password button doesn't contain the good translation.");
    }

    /**
     * Test the overriding of edit action views in fr.
     *
     * @return void
     */
    public function testEditActionFr(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // The router musts redirect to the profile show page.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The HTTP code expected is not ok.");
        $this->assertEquals("/account/profile/", $client->getRequest()->getRequestUri(), "1.2 The uri expected is not ok.");

        // Redirect the the edit page
        $crawler = $client->request("GET", $client->getRequest()->getRequestUri() . "edit");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.3 The HTTP code expected is not ok.");
        $this->assertEquals("/account/profile/edit", $client->getRequest()->getRequestUri(), "1.4 The uri expected is not ok.");

        $this->assertContains("Modification de mon compte", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("Modification de mon compte", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");

        $this->assertContains('<form name="fos_user_profile_form"', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `form` named [fos_user_profile_form].");
        $this->assertContains('<div id="actions">', $crawler->filter("#admin-container form")->html(), "2.4 The page doesn't contain a `div` with `actions` css id.");
        $this->assertCount(2, $crawler->filter("#actions")->children(), "2.5 The page doesn't contain 2 buttons.");
        $this->assertContains('<i class="fas fa-chevron-left"></i> Retour', $crawler->filter("#user-profile-show")->html(), "2.6 The return button doesn't contain the good translation.");
        $this->assertContains('<i class="far fa-save"></i> Mettre à jour', $crawler->filter("#user-profile-save")->html(), "2.7 The save button doesn't contain the good translation.");
    }

    /**
     * Test the overriding of edit action views in en.
     *
     * @return void
     */
    public function testEditActionEn(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // Redirect to the english page in .
        $crawler = $client->request("GET", "/en/account/profile/edit");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1.1 The HTTP code expected is not ok.");
        $this->assertEquals("/en/account/profile/edit", $client->getRequest()->getRequestUri(), "1.2 The uri expected is not ok.");

        $this->assertContains("Edit my account", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("Edit my account", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");

        $this->assertContains('<form name="fos_user_profile_form"', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `form` named [fos_user_profile_form].");
        $this->assertContains('<div id="actions">', $crawler->filter("#admin-container form")->html(), "2.4 The page doesn't contain a `div` with `actions` css id.");
        $this->assertCount(2, $crawler->filter("#actions")->children(), "2.5 The page doesn't contain 2 buttons.");
        $this->assertContains('Return', $crawler->filter("#user-profile-show")->text(), "2.6 The return button doesn't contain the good translation.");
        $this->assertContains('Update', $crawler->filter("#user-profile-save")->text(), "2.7 The save button doesn't contain the good translation.");
    }

    /**
     * Test complete scenario of profile controller (show => click => edit => update) in french.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        // Login as user.
        $crawler = $this->logInUser();
        $client = $this->getClient();

        // The profile page has to respond.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");

        // Click on edit button
        $crawler = $client->click($crawler->selectLink("Modifier")->link());

        // Fill the form and submit it
        $form = $crawler->selectButton("Mettre à jour")->form([
            'fos_user_profile_form[username]' => "user",
            'fos_user_profile_form[email]' => 'user@nairus.fr',
            'fos_user_profile_form[current_password]' => "userpass"
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.fos_user_user_show:contains("user@nairus.fr")')->count(), '2. Missing element td:contains("Test titre")');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.1. The [success] flash message is missing.");
        $this->assertContains("Le profil a été mis à jour.", $crawler->filter('.message-container')->text(), "3.2. The success message is not ok");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-info')->count(), "4.1. The [info] flash message is missing.");
        $this->assertContains("Un email de confirmation vous sera envoyé prochainement", $crawler->filter('.message-container')->text(), "4.2. The success message is not ok");
    }

    /**
     * Test complete scenario in english.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // Redirect to the english page in .
        $client->request("GET", "/en/account/profile");
        $crawler = $client->followRedirect();

        // The profile page has to respond.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP code expected is not ok.");

        // Click on edit button
        $crawler = $client->click($crawler->selectLink("Edit")->link());

        // Fill the form and submit it
        $form = $crawler->selectButton("Update")->form([
            'fos_user_profile_form[username]' => "user",
            'fos_user_profile_form[email]' => 'user@nairus.com',
            'fos_user_profile_form[current_password]' => "userpass"
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.fos_user_user_show:contains("user@nairus.com")')->count(), '2. Missing element td:contains("Test titre")');

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "3.1. The [success] flash message is missing.");
        $this->assertContains("The profile has been updated.", $crawler->filter('.message-container')->text(), "3.2. The success message is not ok");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-info')->count(), "4.1. The [info] flash message is missing.");
        $this->assertContains("A confirmation email will be sent soon", $crawler->filter('.message-container')->text(), "4.2. The success message is not ok");
    }

}
