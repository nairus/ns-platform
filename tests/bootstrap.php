<?php
/**
 * Fichier de bootstrap pour les tests unitaires et fonctionnels.
 */

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';

require_once __DIR__ . '/../var/bootstrap.php.cache';


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$cacheDir = $kernel->getCacheDir();

$application = new Application($kernel);
$application->setAutoExit(false);



deleteDatabase($cacheDir);
executeCommand($application, "doctrine:schema:create");
executeCommand($application,
    "doctrine:fixtures:load", [
        "--fixtures" => [
            "src/Com/Nairus/ResumeBundle/Tests/DataFixtures/ORM"
        ]
    ]
);
backupDatabase($cacheDir);

function executeCommand($application, $command, array $options = []) {
    $options["--env"] = "test";
    $options["--quiet"] = false;
    $options["--verbose"] = 3;
    $appOpts = array_merge($options, ['command' => $command]);

    $application->run(new ArrayInput($appOpts));
}

function deleteDatabase($cacheDir) {
    $folder = $cacheDir . "/";
    foreach(array('ns_test.db','ns_test.db.bk', 'test.db','test.db.bk') AS $file){
        if(file_exists($folder . $file)){
            unlink($folder . $file);
        }
    }
}

function backupDatabase($cacheDir) {
    copy($cacheDir . '/ns_test.db', $cacheDir . '/ns_test.db.bk');
}

function restoreDatabase($cacheDir) {
    copy($cacheDir . '/ns_test.db.bk', $cacheDir . '/ns_test.db');
}