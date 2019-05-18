<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Entity\ContactMessage;

/**
 * Test of ContactMessageRepository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ContactMessageRepository
     */
    private static $repository;

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        self::$repository = self::$em->getRepository(ContactMessage::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Truncate the datas table.
        $this->cleanDatas(static::$container, [ContactMessage::class]);

        parent::tearDown();
    }

    /**
     * Test Create Read Update Delete entity.
     *
     * @return void
     */
    public function testCrud(): void {
        // 1. insert the required fields
        $this->assertCount(0, self::$repository->findAll(), "1. No entity is expected in the database.");

        $contactMessage = new ContactMessage();
        $contactMessage->setRequestDate(new \DateTime("1970-01-01"))
                ->setIp("127.0.0.1")
                ->setName("Nicolas Surian")
                ->setMessage("This is a message");
        static::$em->persist($contactMessage);
        static::$em->flush($contactMessage);
        static::$em->clear(ContactMessage::class);

        // 2. read the entity
        $entities = self::$repository->findAll();
        $this->assertCount(1, $entities, "2.1 One entity is expected in the collection");
        /* @var $newContactMessage ContactMessage */
        $newContactMessage = $entities[0];
        $this->assertEquals("127.0.0.1", $newContactMessage->getIp(), "2.2 The `ip` field is not ok");
        $this->assertEquals("Nicolas Surian", $newContactMessage->getName(), "2.3 The `name` field is not ok");
        $this->assertInstanceOf(\DateTimeImmutable::class, $newContactMessage->getRequestDate(), "2.4 The `dateRequest` field is not a [DateTimeImmutable] object");
        $this->assertEquals("1970-01-01 00:00:00", $newContactMessage->getRequestDate()->format("Y-m-d H:i:s"), "2.5 The `dateRequest` is not ok");
        $this->assertNull($newContactMessage->getEmail(), "2.6 The `email` field is not null");
        $this->assertNull($newContactMessage->getPhone(), "2.7 The `phone` field is not null");
        $this->assertRegExp("~^[0-9]+$~", $newContactMessage->getId(), "2.8 The `id` field is not ok.");

        // 3. update the entity
        $newContactMessage->setEmail("me@email.com")
                ->setPhone("06 01 02 03 04");
        static::$em->flush($newContactMessage);
        static::$em->clear(ContactMessage::class);

        /* @var $updatedContactMessage ContactMessage */
        $updatedContactMessage = static::$repository->find($newContactMessage->getId());
        $this->assertNotNull($updatedContactMessage->getEmail(), "3.1 The `email` field is null");
        $this->assertEquals("me@email.com", $updatedContactMessage->getEmail(), "3.2 The `email` field is not ok");
        $this->assertNotNull($updatedContactMessage->getPhone(), "3.3 The `phone` field is null");
        $this->assertEquals("06 01 02 03 04", $updatedContactMessage->getPhone(), "3.4 The `phone` field is not ok");

        // 4. delete the entity
        $contactMessageId = $updatedContactMessage->getId();
        static::$em->remove($updatedContactMessage);
        static::$em->flush($updatedContactMessage);
        static::$em->clear(ContactMessage::class);
        $this->assertNull(static::$repository->find($contactMessageId), "4.1 The entity has to be removed");
    }

    /**
     * Test the <code>isFlood</code> method.
     *
     * @return void
     */
    public function testIsFlood(): void {
        $contactMessage = new ContactMessage();
        $contactMessage->setRequestDate(new \DateTime("1970-01-01"))
                ->setIp("127.0.0.1")
                ->setName("Nicolas Surian")
                ->setMessage("Lorem ipsum dolor sit amet, consectetur cras amet. ")
                ->setRequestDate(new \DateTimeImmutable());

        $this->assertFalse(self::$repository->isFlood($contactMessage, 3600), "1. No flood attack has to be detected.");

        // insert one message
        static::$em->persist($contactMessage);
        static::$em->flush($contactMessage);
        static::$em->clear(ContactMessage::class);

        $this->assertTrue(self::$repository->isFlood($contactMessage, 3600), "2. A flood attack has to be detected.");
    }

}
