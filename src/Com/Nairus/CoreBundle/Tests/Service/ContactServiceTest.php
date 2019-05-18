<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Entity as NSEntity;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Repository as NSRepository;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;

/**
 * Test of ContactService.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactServiceTest extends AbstractKernelTestCase {

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Truncate the datas table.
        $this->cleanDatas(static::$container, [NSEntity\ContactMessage::class]);

        parent::tearDown();
    }

    /**
     * Test the <code>handleContactMessage</code> method.
     *
     * @return void
     */
    public function testHandleContactMessage(): void {
        // 1. nominal case
        $contactService = new ContactService(static::$em, static::$container->get("logger"));

        // prepare the datas to handle
        $contactMessage = new NSEntity\ContactMessage();
        $contactMessage->setEmail("son.goku@dbz.com")
                ->setMessage("Hello world!")
                ->setName("Son Goku");

        $this->assertTrue($contactService->handleContactMessage("127.0.0.1", $contactMessage), "1. The service has to return true");
        /* @var $entity ContactMessage */
        $entity = static::$em->getRepository(NSEntity\ContactMessage::class)->findOneByIp("127.0.0.1");

        $this->assertNotNull($entity, "2.1 The entity has to be inserted in the database.");
        $this->assertEquals("Son Goku", $entity->getName(), "2.2 The name of the contact expected is not ok.");
        $this->assertEquals("Hello world!", $entity->getMessage(), "2.3 The message of the contact expected is not ok.");
    }

    /**
     * Test the <code>handleContactMessage</code> method with a blacklisted ip.
     *
     * @return void
     */
    public function testHandleContactMessageWithBlacklistedIp(): void {
        // 2. first error case
        try {
            // prepare mocks
            $blacklistedIpRepository = $this->createMock(NSRepository\BlacklistedIpRepository::class);
            $blacklistedIpRepository->expects($this->once())
                    ->method("isBlackListed")
                    ->willReturn(true);
            $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
            $entityManager->expects($this->once())
                    ->method('getRepository')
                    ->willReturn($blacklistedIpRepository);
            $entityManager->expects($this->exactly(0))
                    ->method("beginTransaction");

            // prepare the datas to handle
            $contactService = new ContactService($entityManager, static::$container->get("logger"));
            $contactMessage = new NSEntity\ContactMessage();
            $contactMessage->setEmail("son.goku@dbz.com")
                    ->setMessage("Hello world!")
                    ->setName("Son Goku");

            // launch the test.
            $contactService->handleContactMessage("127.0.0.1", $contactMessage);
        } catch (FunctionalException $exc) {
            $this->assertEquals("contact.message.success", $exc->getTranslationKey(), "1.1 The translation key expected is not ok.");
            $this->assertEquals(ContactServiceInterface::IS_BLACKLITED_ERROR_CODE, $exc->getCode(), "1.2 The exception code is not ok.");
        }
    }

    /**
     * Test the <code>handleContactMessage</code> method with an unkown error.
     *
     * @return void
     */
    public function testHandleContactMessageWithUnknownError(): void {
        // 3. second error case
        try {
            // prepare mocks
            $blacklistedIpRepository = $this->createMock(NSRepository\BlacklistedIpRepository::class);
            $blacklistedIpRepository->expects($this->once())
                    ->method("isBlackListed")
                    ->willReturn(false);
            $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
            $entityManager->expects($this->once())
                    ->method('getRepository')
                    ->willReturn($blacklistedIpRepository);
            $entityManager->expects($this->once())
                    ->method("beginTransaction");
            $entityManager->expects($this->once())
                    ->method("persist");
            $entityManager->expects($this->once())
                    ->method("flush")
                    ->willThrowException(new \Exception("Unkown error"));
            $entityManager->expects($this->exactly(0))
                    ->method("commit");
            $entityManager->expects($this->once())
                    ->method("rollback");

            // prepare the datas to handle
            $contactService = new ContactService($entityManager, static::$container->get("logger"));
            $contactMessage = new NSEntity\ContactMessage();
            $contactMessage->setEmail("son.goku@dbz.com")
                    ->setMessage("Hello world!")
                    ->setName("Son Goku");

            // launch the test.
            $contactService->handleContactMessage("127.0.0.1", $contactMessage);
        } catch (FunctionalException $exc) {
            $this->assertEquals("contact.message.error", $exc->getTranslationKey(), "1.1 The translation key expected is not ok.");
            $this->assertEquals(0, $exc->getCode(), "1.2 The exception code is not ok.");
        }
    }

    /**
     * Test the service from IoC config.
     *
     * @return void
     */
    public function testFromIoc(): void {
        $service = static::$container->get("ns_core.contact_service");
        $this->assertNotNull($service, "1. The service has to be in the container.");
        $this->assertInstanceOf(ContactServiceInterface::class, $service, "2. The service has to be an instance of [ContactServiceInterface]");
        $this->assertInstanceOf(ContactService::class, $service, "3. The service has to be an instance of [ContactService]");
    }

}
