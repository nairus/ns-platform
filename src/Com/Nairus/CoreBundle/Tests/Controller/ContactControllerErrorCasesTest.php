<?php

namespace Com\Nairus\CoreBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Service\ContactServiceInterface;

/**
 * ContactController test error cases.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactControllerErrorCasesTest extends BaseWebTestCase {

    /**
     * Mock of the contact service.
     *
     * @var ContactServiceInterface
     */
    private $contactService;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void {
        parent::setUp();

        // Set the mock in in the service container.
        $this->contactService = $this->createMock(\Com\Nairus\CoreBundle\Service\ContactServiceInterface::class);
        if ($this->getClient()->getContainer()->has("ns_core.contact_service")) {
            $this->getClient()->getContainer()->set("ns_core.contact_service", $this->contactService);
        }
    }

    /**
     * Test the blacklisted ip error.
     *
     * @return void
     */
    public function testBlacklistedErrorCase(): void {
        // prepare the mock scenario
        $functionnalException = new FunctionalException("contact.message.success", "Blacklisted IP", ContactServiceInterface::IS_BLACKLITED_ERROR_CODE);
        $this->contactService->expects($this->once())
                ->method("handleContactMessage")
                ->will($this->throwException($functionnalException));

        // prepare the datas to post
        $client = $this->getClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('contact_message_form');
        $parameters = [
            "com_nairus_corebundle_contactmessage" => [
                "name" => "Son Goku",
                "message" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
                "email" => "son.goku@dragonball.super",
                "phone" => "+33 701 020 304",
                "antispam" => "12fc7bb9b8d24566b51a90e748eb35d9",
                "_token" => $csrfToken,
            ]
        ];

        // launch the test
        $client->request("POST", "/contact", $parameters);
        $success = $client->getRequest()->getSession()->getFlashBag()->peek("success");
        $crawler = $client->followRedirect();

        // verify the result
        $this->assertEquals("/", $client->getRequest()->getRequestUri(), "1. The request uri excepted is not ok");
        $this->assertCount(1, $success, "2. One success message is expected");
        $this->assertContains("Merci ! Votre message a bien été envoyé.", $success[0], "3. The success message is not ok");
    }

    /**
     * Test the unknown error in Fr.
     *
     * @return void
     */
    public function testUnknownErrorCaseFr(): void {
        // prepare the mock scenario
        $functionnalException = new FunctionalException("contact.message.error", "Unkown error");
        $this->contactService->expects($this->once())
                ->method("handleContactMessage")
                ->will($this->throwException($functionnalException));

        // prepare the datas to post
        $client = $this->getClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('contact_message_form');
        $parameters = [
            "com_nairus_corebundle_contactmessage" => [
                "name" => "Son Goku",
                "message" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
                "email" => "son.goku@dragonball.super",
                "phone" => "+33 701 020 304",
                "antispam" => "12fc7bb9b8d24566b51a90e748eb35d9",
                "_token" => $csrfToken,
            ]
        ];

        // launch the test
        $client->request("POST", "/contact", $parameters);
        $errors = $client->getRequest()->getSession()->getFlashBag()->peek("error");

        $crawler = $client->followRedirect();
        $this->assertEquals("/", $client->getRequest()->getRequestUri(), "1. The request uri excepted is not ok");
        $this->assertCount(1, $errors, "2. One error is expected");
        $this->assertContains("Une erreur est survenue ! Votre message n'a pas pu être envoyé.", $errors[0], "3. The success message is not ok");
    }

    /**
     * Test the unknown error in Fr.
     *
     * @return void
     */
    public function testUnknownErrorCaseEn(): void {
        // prepare the mock scenario
        $functionnalException = new FunctionalException("contact.message.error", "Unkown error");
        $this->contactService->expects($this->once())
                ->method("handleContactMessage")
                ->will($this->throwException($functionnalException));

        // prepare the datas to post
        $client = $this->getClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('contact_message_form');
        $parameters = [
            "com_nairus_corebundle_contactmessage" => [
                "name" => "Son Goku",
                "message" => "Lorem ipsum dolor sit amet, consectetur cras amet.",
                "email" => "son.goku@dragonball.super",
                "phone" => "+33 701 020 304",
                "antispam" => "12fc7bb9b8d24566b51a90e748eb35d9",
                "_token" => $csrfToken,
            ]
        ];

        // launch the test
        $client->request("POST", "/en/contact", $parameters);
        $errors = $client->getRequest()->getSession()->getFlashBag()->peek("error");
        $crawler = $client->followRedirect();

        // verify the result
        $this->assertEquals("/en/", $client->getRequest()->getRequestUri(), "1. The request uri excepted is not ok");
        $this->assertCount(1, $errors, "2. One error message is expected");
        $this->assertEquals("An error has occurred! Your message could not be sent.", $errors[0], "3. The error message expected is not ok");
    }

}
