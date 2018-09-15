<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    public function registerBundles() {
        $bundles = [
            // Let the symfony bundle load first to prevent some errors when loading other bundle which depends on the framework bundle.
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Com\Nairus\CoreBundle\NSCoreBundle(),
            new Com\Nairus\ResumeBundle\NSResumeBundle(),
            new Com\Nairus\UserBundle\NSUserBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir() {
        return __DIR__;
    }

    public function getCacheDir() {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir() {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
