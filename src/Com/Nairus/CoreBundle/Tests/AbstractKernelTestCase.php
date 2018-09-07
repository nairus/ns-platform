<?php

namespace Com\Nairus\CoreBundle\Tests;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Base class for unit tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractKernelTestCase extends KernelTestCase {

    /**
     * Entity manager instance.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $em;

    /**
     * Container services instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    /**
     * Init the kernel and entity manager for all tests.
     *
     * @return void
     */
    public static function setUpBeforeClass() {
        static::bootKernel();
        static::$container = static::$kernel->getContainer();
        static::$em = static::$container
                ->get("doctrine")
                ->getManager();

        // Active foreign keys constaint for sqlite.
        $rsm = new ResultSetMapping();
        static::$em->createNativeQuery("PRAGMA foreign_keys = ON", $rsm)->execute();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // We force the reset of the entity manager for the next tests.
        // This avoid this kind of errors "Doctrine\ORM\ORMException: The EntityManager is closed."
        // when we launch the entire tests suite.
        static::$container
                ->get("doctrine")
                ->resetManager();

        static::$em = static::$container
                ->get("doctrine")
                ->getManager();
    }

}
