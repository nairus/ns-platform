<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Entity\BlacklistedIp;

/**
 * Test of BlacklistedIpRepository.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BlacklistedIpRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var BlacklistedIpRepository
     */
    private static $repository;

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        self::$repository = self::$em->getRepository(BlacklistedIp::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $blacklistedIp = new BlacklistedIp();
        $blacklistedIp->setIp("127.0.0.1");
        static::$em->persist($blacklistedIp);
        static::$em->flush($blacklistedIp);
        static::$em->clear(BlacklistedIp::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // Truncate the datas table.
        $this->cleanDatas(static::$container, [BlacklistedIp::class]);
    }

    /**
     * Test Create Read Update Delete entity.
     *
     * @return void
     */
    public function testCrud(): void {
        // 1. Test read.
        $entities = static::$repository->findAll();

        $this->assertCount(1, $entities, "1. One entity is expected in the database.");

        // 2. Test create.
        $newIp = new BlacklistedIp();
        $newIp->setIp("127.0.0.2");
        static::$em->persist($newIp);
        static::$em->flush($newIp);
        static::$em->clear(BlacklistedIp::class);

        $this->assertCount(2, static::$repository->findAll(), "2.1. Two entities are expected in the database.");
        /* @var $entityToUpdate BlacklistedIp */
        $entityToUpdate = static::$repository->findOneByIp("127.0.0.2");
        $this->assertNotNull($entityToUpdate, "2.2. The entity has to be not null.");

        // 3. Test update.
        $id = $entityToUpdate->getId();
        $entityToUpdate->setIp("127.0.0.3")
                ->setBlacklistedAt(new \DateTime("2019-01-01 00:00:00"));
        static::$em->flush($entityToUpdate);
        static::$em->clear(BlacklistedIp::class);

        /* @var $entityUpdated BlacklistedIp */
        $entityUpdated = static::$repository->find($id);
        $this->assertNotNull($entityUpdated, "3.1. The entity has to be not null.");
        $this->assertEquals("127.0.0.3", $entityUpdated->getIp(), "3.2. The ip has to be updated.");
        $this->assertEquals("2019-01-01 00:00:00", $entityUpdated->getBlacklistedAt()->format("Y-m-d H:i:s"), "3.3. The date has to be updated");

        // 4. Test delete.
        static::$em->remove($entityUpdated);
        static::$em->flush($entityUpdated);
        static::$em->clear(BlacklistedIp::class);
        $this->assertNull(static::$repository->find($id), "4.1. The entity has to be removed.");
    }

    /**
     * Test the unique constraint on the ip field.
     *
     * @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     *
     * @return void
     */
    public function testUniqueConstraint(): void {
        $blacklistedIp = new BlacklistedIp();
        $blacklistedIp->setIp("127.0.0.1");
        static::$em->persist($blacklistedIp);
        static::$em->flush($blacklistedIp);
    }

    /**
     * Test `isBlackListed` method.
     *
     * @return void
     */
    public function testIsBlackListed(): void {
        $this->assertTrue(static::$repository->isBlackListed("127.0.0.1"));
    }

}
