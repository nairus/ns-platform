<?php

namespace Com\Nairus\UserBundle\Tests\Controller;

use Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase;

/**
 * Functional tests for Security controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SecurityControllerTest extends AbstractUserWebTestCase {

    /**
     * Test login action with user credential.
     *
     * @covers Com\Nairus\UserBundle\Controller\SecurityController::loginAction
     *
     * @return void
     */
    public function testLoginWithUser(): void {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The response has to return a [200] code.");
        $this->assertNotContains('<span class="mx-1 font-weight-bold text-white">Bienvenue', $crawler->filter("#navbar")->html(), "1.2 The navbar hasn't to contain authentication username");
        $this->assertEquals(0, $crawler->filter("#admin-menu")->count(), "1.3 The navbar hasn't to contain admin dropdown button.");
        $this->assertContains("Veuillez vous connecter", $crawler->filter("html > head > title")->text(), "1.4 The page title expected is not ok.");
        $this->assertContains("Veuillez vous connecter", $crawler->filter("h1")->text(), "1.5 The h1 tag expected is not ok.");
        $this->assertContains("Pseudo", $crawler->filter("input#username")->attr("placeholder"), "1.6 The username placeholder is not ok.");
        $this->assertContains("Mot de passe", $crawler->filter("input#password")->attr("placeholder"), "1.7 The password placeholder is not ok.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'user',
            '_password' => 'userpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 Unexpected HTTP status code for GET / with admin login");
        $this->assertContains('Bienvenue user', $crawler->filter("#navbar")->text(), "2.2 The navbar has to contain authentication username");
        $this->assertEquals(1, $crawler->filter("#admin-menu")->count(), "2.3 The navbar has to contain admin dropdown button.");
        $this->assertEquals(2, $crawler->filter(".nav-item > .dropdown-menu > .dropdown-item")->count(), "2.4 The admin dropdown menu has to contain 2 items.");
        $dropdownMenu = $crawler->filter(".nav-item > .dropdown-menu")->text();
        $this->assertContains("Mon compte", $dropdownMenu, "2.5. The dropdown menu has to contain this item.");
        $this->assertContains("Changer mon mot de passe", $dropdownMenu, "2.6. The dropdown menu has to contain this item.");
    }

    /**
     * Test login action with author credential.
     *
     * @return void
     */
    public function testLoginWithAuthor(): void {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/en/login');

        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The response has to return a [200] code.");
        $this->assertNotContains('<span class="mx-1 font-weight-bold text-white">Bienvenue', $crawler->filter("#navbar")->html(), "1.2 The navbar hasn't to contain authentication username");
        $this->assertEquals(0, $crawler->filter("#admin-menu")->count(), "1.3 The navbar hasn't to contain admin dropdown button.");
        $this->assertContains("Please sign in", $crawler->filter("html > head > title")->text(), "1.4 The page title expected is not ok.");
        $this->assertContains("Please sign in", $crawler->filter("h1")->text(), "1.5 The h1 tag expected is not ok.");
        $this->assertContains("Username", $crawler->filter("input#username")->attr("placeholder"), "1.6 The username placeholder is not ok.");
        $this->assertContains("Password", $crawler->filter("input#password")->attr("placeholder"), "1.7 The password placeholder is not ok.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Log in')->form(array(
            '_username' => 'author',
            '_password' => 'authorpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 Unexpected HTTP status code for GET / with admin login");
        $this->assertContains('Welcome author', $crawler->filter("#navbar")->text(), "2.2 The navbar has to contain authentication username");
        $this->assertEquals(1, $crawler->filter("#admin-menu")->count(), "2.3 The navbar has to contain admin dropdown button.");
        $this->assertEquals(2, $crawler->filter(".nav-item > .dropdown-menu > .dropdown-item")->count(), "2.4 The admin dropdown menu has to contain 2 items.");
        $dropdownMenu = $crawler->filter(".nav-item > .dropdown-menu")->text();
        $this->assertContains("My account", $dropdownMenu, "2.5. The dropdown menu has to contain this item.");
        $this->assertContains("Change my password", $dropdownMenu, "2.6. The dropdown menu has to contain this item.");
    }

    /**
     * Test login action with moderator credential.
     *
     * @return void
     */
    public function testLoginWithModerator(): void {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The response has to return a [200] code.");
        $this->assertNotContains('<span class="mx-1 font-weight-bold text-white">Bienvenue', $crawler->filter("#navbar")->html(), "1.2 The navbar hasn't to contain authentication username");
        $this->assertEquals(0, $crawler->filter("#admin-menu")->count(), "1.3 The navbar hasn't to contain admin dropdown button.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'moderator',
            '_password' => 'moderatorpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 Unexpected HTTP status code for GET / with admin login");
        $this->assertContains('Bienvenue moderator', $crawler->filter("#navbar")->text(), "2.2 The navbar has to contain authentication username");
        $this->assertEquals(1, $crawler->filter("#admin-menu")->count(), "2.3 The navbar has to contain admin dropdown button.");
        $this->assertEquals(2, $crawler->filter(".nav-item > .dropdown-menu > .dropdown-item")->count(), "2.4 The admin dropdown menu has to contain 2 items.");
        $dropdownMenu = $crawler->filter(".nav-item > .dropdown-menu")->text();
        $this->assertContains("Mon compte", $dropdownMenu, "2.5. The dropdown menu has to contain this item.");
        $this->assertContains("Changer mon mot de passe", $dropdownMenu, "2.6. The dropdown menu has to contain this item.");
    }

    /**
     * Test login action with admin credential.
     *
     * @return void
     */
    public function testLoginWithAdmin(): void {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/en/login');

        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The response has to return a [200] code.");
        $this->assertNotContains('<span class="mx-1 font-weight-bold text-white">Bienvenue', $crawler->filter("#navbar")->html(), "1.2 The navbar hasn't to contain authentication username");
        $this->assertEquals(0, $crawler->filter("#admin-menu")->count(), "1.3 The navbar hasn't to contain admin dropdown button.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Log in')->form(array(
            '_username' => 'admin',
            '_password' => 'adminpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 Unexpected HTTP status code for GET / with admin login");
        $this->assertContains('Welcome admin', $crawler->filter("#navbar")->text(), "2.2 The navbar has to contain authentication username");
        $this->assertEquals(1, $crawler->filter("#admin-menu")->count(), "2.3 The navbar has to contain admin dropdown button.");
        $this->assertEquals(4, $crawler->filter(".nav-item > .dropdown-menu > .dropdown-item")->count(), "2.4 The admin dropdown menu has to contain 2 items.");
        $dropdownMenu = $crawler->filter(".nav-item > .dropdown-menu")->text();
        $this->assertContains("My account", $dropdownMenu, "2.5. The dropdown menu has to contain this item.");
        $this->assertContains("Change my password", $dropdownMenu, "2.6. The dropdown menu has to contain this item.");
        $this->assertContains("Manage News", $dropdownMenu, "2.7. The dropdown menu has to contain this item.");
        $this->assertContains("Manage Skills", $dropdownMenu, "2.8. The dropdown menu has to contain this item.");
    }

    /**
     * Test login action with sadmin credential.
     *
     * @return void
     */
    public function testLoginWithSAdmin(): void {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(), "1.1 The response has to return a [200] code.");
        $this->assertNotContains('<span class="mx-1 font-weight-bold text-white">Bienvenue', $crawler->filter("#navbar")->html(), "1.2 The navbar hasn't to contain authentication username");
        $this->assertEquals(0, $crawler->filter("#admin-menu")->count(), "1.3 The navbar hasn't to contain admin dropdown button.");

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'sadmin',
            '_password' => 'sadminpass',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "2.1 Unexpected HTTP status code for GET / with admin login");
        $this->assertContains('Bienvenue sadmin', $crawler->filter("#navbar")->text(), "2.2 The navbar has to contain authentication username");
        $this->assertEquals(1, $crawler->filter("#admin-menu")->count(), "2.3 The navbar has to contain admin dropdown button.");
        $this->assertEquals(4, $crawler->filter(".nav-item > .dropdown-menu > .dropdown-item")->count(), "2.4 The admin dropdown menu has to contain 2 items.");
        $dropdownMenu = $crawler->filter(".nav-item > .dropdown-menu")->text();
        $this->assertContains("Mon compte", $dropdownMenu, "2.5. The dropdown menu has to contain this item.");
        $this->assertContains("Changer mon mot de passe", $dropdownMenu, "2.6. The dropdown menu has to contain this item.");
        $this->assertContains("Gestion des News", $dropdownMenu, "2.7. The dropdown menu has to contain this item.");
        $this->assertContains("Gestion des compétences", $dropdownMenu, "2.8. The dropdown menu has to contain this item.");
    }

}
