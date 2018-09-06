<?php

namespace Com\Nairus\UserBundle\Tests;

use Com\Nairus\UserBundle\Entity\User;
use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Translation\TranslatorInterface;

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
     * The translator service.
     *
     * @var TranslatorInterface
     */
    private $translator;

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
        $this->translator = static::$kernel->getContainer()->get('translator');
    }

    public function tearDown(): void {
        // Free the resources.
        unset($this->client);
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
     * @param string $locale the locale to force in url.
     *
     * @return Crawler
     */
    protected function logInUser($locale = null): Crawler {
        $user = new User();
        $user->setUsername("user")
                ->setPassword("userpass")
                ->setEmail("user@test.com")
                ->addRole(static::$users["user"]);

        return $this->login($user, $locale);
    }

    /**
     * Log with author credential.
     *
     * @param string $locale the locale to force in url.
     *
     * @return Crawler
     */
    protected function logInAuthor($locale = null): Crawler {
        $user = new User();
        $user->setUsername("author")
                ->setPassword("authorpass")
                ->setEmail("author@test.com")
                ->addRole(static::$users["author"]);

        return $this->login($user, $locale);
    }

    /**
     * Log with moderator credential.
     *
     * @param string $locale the locale to force in url.
     *
     * @return Crawler
     */
    protected function logInModerator($locale = null): Crawler {
        $user = new User();
        $user->setUsername("moderator")
                ->setPassword("moderatorpass")
                ->setEmail("moderator@test.com")
                ->addRole(static::$users["moderator"]);

        return $this->login($user, $locale);
    }

    /**
     * Login with admin credentials.
     *
     * @param string $locale the locale to force in url.
     *
     * @return Crawler
     */
    protected function logInAdmin($locale = null): Crawler {
        $user = new User();
        $user->setUsername("admin")
                ->setPassword("adminpass")
                ->setEmail("admin@test.com")
                ->addRole(static::$users["admin"]);

        return $this->login($user, $locale);
    }

    /**
     * Login with sadmin credentials.
     *
     * @param string $locale the locale to force in url.
     *
     * @return Crawler
     */
    protected function logInSuperAdmin($locale = null): Crawler {
        $user = new User();
        $user->setUsername("sadmin")
                ->setPassword("sadminpass")
                ->setEmail("sadmin@test.com")
                ->addRole(static::$users["sadmin"]);

        return $this->login($user, $locale);
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
     * @param User   $user    the current user.
     * @param string $locale  the locale to force in url.
     *
     * @return Crawler
     */
    private function login(User $user, string $locale = null): Crawler {
        $localeParameter = '';
        $loginButtonLabel = 'Connexion';

        // If locale is not null we add it in the uri
        // and change the login button label.
        if (null !== $locale) {
            $localeParameter = '/' . $locale;
            $loginButtonLabel = $this->translator->trans(
                    "security.login.submit", [],
                    'FOSUserBundle', $locale
            );
        }

        $crawler = $this->client->request('GET', $localeParameter . '/login');

        // Try authentication with good credential.
        // Fill in the form and submit it
        $form = $crawler->selectButton($loginButtonLabel)->form(array(
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword(),
        ));

        $this->client->submit($form);
        return $this->client->followRedirect();
    }

}
