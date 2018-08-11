<?php

namespace Com\Nairus\ResumeBundle\Tests;

/**
 * @todo Faire UserBundle avec UserRoleEnum
 */
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class abstraite des tests WEB avec utilisateur.
 *
 * @author nairus
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
    protected $users = [
        ["user", "userpass", "user@test.fr", UserRolesEnum::AUTHOR],
        ["admin", "adminpass", "admin@test.fr", UserRolesEnum::ADMIN],
        ["sadmin", "sadminpass", "sadmin@test.fr", UserRolesEnum::SUPER_ADMIN],
    ];

    public function setUp() {
        $this->client = static::createClient();
    }

    protected function logInAuthor() {
        $session = $this->client->getContainer()->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'main';

        $user = new User();
        $user->setUsername($this->users[0][0])
                ->setId(1)
                ->setPassword($this->users[0][1])
                ->setEmail($this->users[0][2])
                ->addRole($this->users[0][3]);

        $token = new UsernamePasswordToken($user, $this->users[0][1], $firewallContext, [$this->users[0][3]]);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function logInAdmin() {
        $session = $this->client->getContainer()->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'main';
        $user = new User();
        $user->setUsername($this->users[1][0])
                ->setId(2)
                ->setPassword($this->users[1][1])
                ->setEmail($this->users[1][2])
                ->addRole($this->users[1][3]);

        $token = new UsernamePasswordToken($user, $this->users[1][1], $firewallContext, [$this->users[1][3]]);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function logInSuperAdmin() {
        $session = $this->client->getContainer()->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'main';
        $user = new User();
        $user->setUsername($this->users[2][0])
                ->setId(3)
                ->setPassword($this->users[2][1])
                ->setEmail($this->users[2][2])
                ->addRole($this->users[2][3]);

        $token = new UsernamePasswordToken($user, $this->users[2][1], $firewallContext, [$this->users[2][3]]);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Tente une déconnexion et renvoie le retour de la page.
     *
     * @return void
     */
    protected function logout() {
        $this->client->request('GET', '/logout');
    }

    /**
     * Retourne le client http de test.
     *
     * @return Client
     */
    public function getClient() {
        return $this->client;
    }

}
