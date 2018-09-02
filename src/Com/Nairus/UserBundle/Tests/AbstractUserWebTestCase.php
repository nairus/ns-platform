<?php

namespace Com\Nairus\UserBundle\Tests;

use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Abstract test class with user's credentials.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AbstractUserWebTestCase extends WebTestCase {

    /**
     * Client HTTP de test.
     *
     * @var Client
     */
    private $client = null;

    /**
     * Liste d'utilisateurs de test.
     *
     * @var User[]
     */
    public static $users = [
        "user" => UserRolesEnum::USER,
        "author" => UserRolesEnum::AUTHOR,
        "moderator" => UserRolesEnum::MODERATOR,
        "admin" => UserRolesEnum::ADMIN,
        "sadmin" => UserRolesEnum::SUPER_ADMIN
    ];

    public function setUp(): void {
        $this->client = static::createClient();
    }

    /**
     * Return the test HTTP client.
     *
     * @return Client
     */
    public function getClient(): Client {
        return $this->client;
    }

    /**
     * Log with user credential.
     *
     * @return void
     */
    protected function logInUser(): Crawler {
        $user = new User();
        $user->setUsername("user")
                ->setPassword("userpass")
                ->setEmail("user@test.com")
                ->addRole(static::$users["user"]);

        return $this->login($user);
    }

    /**
     * Log with author credential.
     *
     * @return void
     */
    protected function logInAuthor(): Crawler {
        $user = new User();
        $user->setUsername("author")
                ->setPassword("authorpass")
                ->setEmail("author@test.com")
                ->addRole(static::$users["author"]);

        return $this->login($user);
    }

    /**
     * Log with moderator credential.
     *
     * @return void
     */
    protected function logInModerator(): Crawler {
        $user = new User();
        $user->setUsername("moderator")
                ->setPassword("moderatorpass")
                ->setEmail("moderator@test.com")
                ->addRole(static::$users["moderator"]);

        return $this->login($user);
    }

    /**
     * Login with admin credentials.
     *
     * @return void
     */
    protected function logInAdmin(): Crawler {
        $user = new User();
        $user->setUsername("admin")
                ->setPassword("adminpass")
                ->setEmail("admin@test.com")
                ->addRole(static::$users["admin"]);

        return $this->login($user);
    }

    /**
     * Login with sadmin credentials.
     *
     * @return void
     */
    protected function logInSuperAdmin(): Crawler {
        $user = new User();
        $user->setUsername("sadmin")
                ->setPassword("sadminpass")
                ->setEmail("sadmin@test.com")
                ->addRole(static::$users["sadmin"]);

        return $this->login($user);
    }

    /**
     * Try to logout.
     *
     * @return void
     */
    protected function logout() {
        $this->client->request('GET', '/logout');
    }

    /**
     * Simulate the login action.
     *
     * @param User $user the current user.
     */
    private function login(User $user): Crawler {
        $crawler = $this->client->request('GET', '/login');

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword(),
        ));

        $this->client->submit($form);
        return $this->client->followRedirect();
    }

}
