<?php

namespace Com\Nairus\CoreBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Base class test for simple functional tests (for public controllers).
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class BaseWebTestCase extends WebTestCase {

    /**
     * Test HTTP Client.
     *
     * @var Client
     */
    private $client = null;

    /**
     * Init the HTTP test client for each tests.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->client = static::createClient();
    }

    /**
     * Return the HTTP Test client.
     *
     * @return Client
     */
    protected function getClient(): Client {
        return $this->client;
    }

    /**
     * Return the entity manager.
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface {
        return $this->client->getContainer()->get("doctrine")->getManager();
    }

}
