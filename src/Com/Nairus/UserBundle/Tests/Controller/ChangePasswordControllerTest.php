<?php

namespace Com\Nairus\UserBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Test of ChangePasswordController.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ChangePasswordControllerTest extends AbstractUserWebTestCase {

    /**
     * Users manager instance.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * Entity manager instance.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void {
        parent::setUp();
        $this->userManager = static::$kernel->getContainer()->get("fos_user.user_manager");
        $this->em = static::$kernel->getContainer()
                ->get("doctrine")
                ->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void {
        // Free the resources.
        parent::tearDown();
        unset($this->userManager);
    }

    /**
     * Test the changePasswordAction overrided views in fr.
     *
     * @return void
     */
    public function testChangePasswordActionFr(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // Redirect to changePassword form.
        $crawler = $client->request("GET", "/account/change-password");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP status code excepected is not ok.");

        // Test the translation and the overrided view.
        $this->assertContains("Modification de mon mot de passe", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("Modification de mon mot de passe", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");
        $this->assertContains('<div class="actions">', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `div` with `actions` css class.");
        $this->assertEquals(2, $crawler->filter("#admin-container .actions")->children()->count(), "2.4 The page doesn't contain 2 buttons.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("<i class=\"fas fa-chevron-left\"></i>\n        Retour", $adminContainer, "2.5 The return button doesn't contain the good translation.");
        $this->assertContains("<i class=\"fas fa-user-shield\"></i>\n        Modifier le mot de passe", $adminContainer, "2.6 The change password button doesn't contain the good translation.");
        $this->assertContains('<form name="fos_user_change_password_form" method="post"', $adminContainer, "2.7 The page doesn't contain the form expected.");
    }

    /**
     * Test the changePasswordAction overrided views in en.
     *
     * @return void
     */
    public function testChangePasswordActionEn(): void {
        // Login as user.
        $this->logInUser();
        $client = $this->getClient();

        // Redirect to changePassword form.
        $crawler = $client->request("GET", "/en/account/change-password");
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP status code excepected is not ok.");

        // Test the translation and the overrided view.
        $this->assertContains("Modification of my password", $crawler->filter("html > head > title")->text(), "2.1 The page title expected is not ok.");
        $this->assertContains("Modification of my password", $crawler->filter("h1")->text(), "2.2 The h1 tag expected is not ok.");
        $this->assertContains('<div class="actions">', $crawler->filter("#admin-container")->html(), "2.3 The page doesn't contain a `div` with `actions` css class.");
        $this->assertEquals(2, $crawler->filter("#admin-container .actions")->children()->count(), "2.4 The page doesn't contain 2 buttons.");
        $adminContainer = $crawler->filter("#admin-container")->html();
        $this->assertContains("<i class=\"fas fa-chevron-left\"></i>\n        Return", $adminContainer, "2.5 The return button doesn't contain the good translation.");
        $this->assertContains("<i class=\"fas fa-user-shield\"></i>\n        Change password", $adminContainer, "2.6 The change password button doesn't contain the good translation.");
        $this->assertContains('<form name="fos_user_change_password_form" method="post"', $adminContainer, "2.7 The page doesn't contain the form expected.");
    }

    /**
     * Test complete scenarion in fr.
     *
     * @return void
     */
    public function testCompleteScenarioFr(): void {
        // Login as user.
        $crawler = $this->logInUser();
        $client = $this->getClient();

        // Redirect to profile show page.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP status code excepected is not ok.");

        // Click on change password button
        $crawler = $client->click($crawler->selectLink("Changer mon mot de passe")->link());

        // Fill the form and submit it
        $form = $crawler->selectButton("Modifier le mot de passe")->form([
            'fos_user_change_password_form[current_password]' => "userpass",
            'fos_user_change_password_form[plainPassword][first]' => 'userpass2',
            'fos_user_change_password_form[plainPassword][second]' => "userpass2"
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.1. The [success] flash message is missing.");
        $this->assertContains("Le mot de passe a été modifié.", $crawler->filter('.message-container')->text(), "2.2. The success message is not ok");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-info')->count(), "3.1. The [info] flash message is missing.");
        $this->assertContains("Un email de confirmation vous sera envoyé prochainement", $crawler->filter('.message-container')->text(), "3.2. The success message is not ok");

        // Check if the password changed.
        $this->logout();

        // Try to login
        $crawler = $this->logInUser();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "4.1. The status code expected is not ok.");
        $this->assertEquals('/login', $client->getRequest()->getRequestUri(), "4.2. The request uri expected is not ok.");

        $this->assertContains("Identifiants invalides.", $crawler->filter("form")->text(), "4.3. The error message should be shown.");
        $this->assertEquals(2, $crawler->filter(".is-invalid")->count(), "4.4. The inputs should be marked as invalid.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'user',
            '_password' => 'userpass2',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "5.1 Unexpected HTTP status code.");
        $this->assertEquals('/account/profile/', $client->getRequest()->getRequestUri(), "5.2. The request uri expected is not ok.");

        // Reset old password
        /* @var $user \Com\Nairus\UserBundle\Entity\User */
        $user = $this->userManager->findUserByUsername("user");
        $user->setPlainPassword("userpass");
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);
    }

    /**
     * Test complete scenario en.
     *
     * @return void
     */
    public function testCompleteScenarioEn(): void {
        // Login as user.
        $crawler = $this->logInUser('en');
        $client = $this->getClient();

        // Redirect to profile show page.
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "1. The HTTP status code excepected is not ok.");

        // Click on change password button
        $crawler = $client->click($crawler->selectLink("Change my password")->link());

        // Fill the form and submit it
        $form = $crawler->selectButton("Change password")->form([
            'fos_user_change_password_form[current_password]' => "userpass",
            'fos_user_change_password_form[plainPassword][first]' => 'userpass2',
            'fos_user_change_password_form[plainPassword][second]' => "userpass2"
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the flash message
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-success')->count(), "2.1. The [success] flash message is missing.");
        $this->assertContains("The password has been changed.", $crawler->filter('.message-container')->text(), "2.2. The success message is not ok");
        $this->assertGreaterThan(0, $crawler->filter('.message-container > .alert-info')->count(), "3.1. The [info] flash message is missing.");
        $this->assertContains("A confirmation email will be sent soon", $crawler->filter('.message-container')->text(), "3.2. The success message is not ok");

        // Check if the password changed.
        $this->logout();

        // Try to login
        $crawler = $this->logInUser('en');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "4.1. The status code expected is not ok.");
        $this->assertEquals('/en/login', $client->getRequest()->getRequestUri(), "4.2. The request uri expected is not ok.");

        $this->assertContains("Invalid credentials.", $crawler->filter("form")->text(), "4.3. The error message should be shown.");
        $this->assertEquals(2, $crawler->filter(".is-invalid")->count(), "4.4. The inputs should be marked as invalid.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Log in')->form(array(
            '_username' => 'user',
            '_password' => 'userpass2',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "5.1 Unexpected HTTP status code.");
        $this->assertEquals('/en/account/profile/', $client->getRequest()->getRequestUri(), "5.2. The request uri expected is not ok.");

        // Reset old password
        /* @var $user \Com\Nairus\UserBundle\Entity\User */
        $user = $this->userManager->findUserByUsername("user");
        $user->setPlainPassword("userpass");
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);
    }

}
