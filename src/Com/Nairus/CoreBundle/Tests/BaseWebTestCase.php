<?php

namespace Com\Nairus\CoreBundle\Tests;

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
    public function setUp(): void {
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

}
