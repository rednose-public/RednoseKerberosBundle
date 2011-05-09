<?php

namespace Rednose\KerberosBundle\DependencyInjection;

use
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\Config\FileLocator;

class RednoseKerberosExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
                $container,
                new FileLocator(__DIR__.'/../Resources/config')
            );
        $loader->load('security.xml');
    }
}
