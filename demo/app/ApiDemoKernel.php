<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class ApiDemoKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\WebServerBundle\WebServerBundle(),
            new Symfony\Bundle\DebugBundle\DebugBundle(),

            new Ynlo\RestfulPlatformBundle\RestfulPlatformBundle(),

            //dependencies
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),//TODO: make optional

            new Ynlo\RestfulPlatformBundle\Demo\ApiDemoBundle\ApiDemoBundle(),
        ];


        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
