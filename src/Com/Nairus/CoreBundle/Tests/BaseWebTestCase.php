<?php

namespace Com\Nairus\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class BaseWebTestCase extends WebTestCase {

    /**
     * Test HTTP Client.
     *
     * @var Client
     */
    private $client = null;

    public function setUp() {
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
