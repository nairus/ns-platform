<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Entity as NSEntity;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Repository as NSRepository;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadContactMessage;

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
        parent::tearDown();

        // Truncate the datas table.
        $this->cleanDatas(static::$container, [NSEntity\ContactMessage::class, NSEntity\BlacklistedIp::class]);
    }

    /**
     * Test the `handleContactMessage` method.
     *
     * @return void
     */
    public function testHandleContactMessage(): void {
        // 1. nominal case
        $contactService = $this->getService();

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
     * Test the `handleContactMessage` method with a blacklisted ip.
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
            $paramsMap = [
                [NSEntity\BlacklistedIp::class, $blacklistedIpRepository],
                [NSEntity\ContactMessage::class, $this->createMock(NSRepository\ContactMessageRepository::class)]
            ];
            $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
            $entityManager->expects($this->exactly(2))
                    ->method('getRepository')
                    ->willReturnMap($paramsMap);
            $entityManager->expects($this->exactly(0))
                    ->method("beginTransaction");

            // prepare the datas to handle
            $contactService = $this->getService($entityManager);
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
     * Test the `handleContactMessage` method with an unkown error.
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
            $paramsMap = [
                [NSEntity\BlacklistedIp::class, $blacklistedIpRepository],
                [NSEntity\ContactMessage::class, $this->createMock(NSRepository\ContactMessageRepository::class)]
            ];
            $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
            $entityManager->expects($this->exactly(2))
                    ->method('getRepository')
                    ->willReturnMap($paramsMap);
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
            $contactService = $this->getService($entityManager);
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

    /**
     * Test the `findAllForPage` method.
     *
     * @return void
     */
    public function testFindAllForPage(): void {
        // load datas for testing
        $loadContactMessage = new LoadContactMessage();
        $loadContactMessage->load(static::$em);

        // test for page 1
        $contactService = $this->getService();
        $dtoPage1 = $contactService->findAllForPage(1, 2);

        // verify for page 1
        $this->assertNotNull($dtoPage1, "1.1 The dto expected is null");
        $this->assertSame(2, $dtoPage1->getPages(), "1.2 Two pages are expected");
        $this->assertCount(2, $dtoPage1->getEntities(), "1.3 Two entities are expected in the collection");
        $this->assertCount(1, $dtoPage1->getBlacklistedIps(), "1.4 One blacklisted ip is expected");
        $this->assertArrayHasKey("127.0.0.3", $dtoPage1->getBlacklistedIps(), "1.5 The ip expected is not ok.");

        // test for page 2
        $dtoPage2 = $contactService->findAllForPage(2, 2);

        // verify for page 1
        $this->assertNotNull($dtoPage2, "2.1 The dto expected is null");
        $this->assertSame(2, $dtoPage2->getPages(), "2.2 Two pages are expected");
        $this->assertCount(1, $dtoPage2->getEntities(), "2.3 One entity expected in the collection");
        $this->assertCount(1, $dtoPage2->getBlacklistedIps(), "2.4 One blacklisted ip is expected");
        $this->assertArrayHasKey("127.0.0.1", $dtoPage2->getBlacklistedIps(), "2.5 The ip expected is not ok.");
    }

    /**
     * Test the `findAllForPage` method.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\PaginatorException
     *
     * @return void
     */
    public function testFindAllForPageWithBadPage(): void {
        $contactService = $this->getService();
        $contactService->findAllForPage(0, 2);
    }

    /**
     * Test `blacklistContactMessage` method.
     *
     * @return void
     */
    public function testBlacklistContactMessage(): void {
        // load datas for testing
        $loadContactMessage = new LoadContactMessage();
        $loadContactMessage->load(static::$em);

        // get the entity to blacklist.
        $contactMessage = static::$em->getRepository(NSEntity\ContactMessage::class)->findOneByIp("127.0.0.2");

        // launch the test.
        $service = $this->getService();
        $this->assertTrue($service->blacklistContactMessage($contactMessage), "1. The ip has to be blacklisted");
        $this->assertFalse($service->blacklistContactMessage($contactMessage), "2. The ip has not to be blacklisted");
    }

    /**
     * Test `deleteContactMessage` method.
     *
     * @return void
     */
    public function testDeleteContactMessage(): void {
        // load datas for testing
        $loadContactMessage = new LoadContactMessage();
        $loadContactMessage->load(static::$em);

        // get the entity to blacklist.
        /* @var $contactMessage NSEntity\ContactMessage */
        $contactMessage = static::$em->getRepository(NSEntity\ContactMessage::class)->findOneByIp("127.0.0.2");
        $id = $contactMessage->getId();

        // launch the test.
        $service = $this->getService();
        $idDeleted = $service->deleteContactMessage($contactMessage);

        // verify the test.
        $this->assertSame($id, $idDeleted, "1. The id expected is not ok.");
        $this->assertNull(static::$em->find(NSEntity\ContactMessage::class, $id), "2. The entity has to be deleted in database.");
    }

    /**
     * Create and return the service instance.
     *
     * @param EntityManagerInterface $em
     *
     * @return ContactService
     */
    private function getService(\Doctrine\ORM\EntityManagerInterface $em = null): ContactService {
        $entityManager = $em ? $em : static::$em;
        return new ContactService($entityManager, static::$container->get("logger"));
    }

}
