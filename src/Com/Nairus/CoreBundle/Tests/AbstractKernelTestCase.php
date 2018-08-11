<?php

namespace Com\Nairus\CoreBundle\Tests;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Classe de base des tests unitaires du bundle.
 *
 * @author nairus
 */
abstract class AbstractKernelTestCase extends KernelTestCase {

    /**
     * Instance du gestionnaire des entités.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $em;

    /**
     * Instance du conteneur de services.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    public static function setUpBeforeClass() {
        static::bootKernel();
        static::$container = static::$kernel->getContainer();
        static::$em = static::$container
                ->get("doctrine")
                ->getManager();

        // On active la gestion des clés étrangères pour sqlite.
        $rsm = new ResultSetMapping();
        static::$em->createNativeQuery("PRAGMA foreign_keys = ON", $rsm)->execute();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // On force le reset de l'entity manager pour les tests suivants.
        // Cela évite des erreurs du genre "Doctrine\ORM\ORMException: The EntityManager is closed."
        // quand on lance la suite de tests entière.
        static::$container
                ->get("doctrine")
                ->resetManager();

        static::$em = static::$container
                ->get("doctrine")
                ->getManager();
    }

}
