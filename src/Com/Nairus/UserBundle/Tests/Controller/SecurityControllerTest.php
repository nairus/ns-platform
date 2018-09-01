<?php

namespace Com\Nairus\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests for Security controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SecurityControllerTest extends WebTestCase {

    /**
     * Test login action.
     */
    public function testLogin() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->markTestIncomplete("TODO");
    }

}
