<?php

namespace Rednose\KerberosBundle\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class KerberosFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'security.authentication.provider.kerberos.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.kerberos'))
            ->replaceArgument(0, new Reference($userProvider))
            ->addArgument($id);

        $listenerId = 'security.authentication.listener.kerberos.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.kerberos'));
        $listener->replaceArgument(2, $id);
        $listener->replaceArgument(3, $config['user_key']);
        $listener->replaceArgument(4, $config['default_user']);

        return array($provider, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'kerberos';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('provider')->end()
                ->scalarNode('user_key')->defaultValue('REMOTE_USER')->end()
                ->scalarNode('default_user')->end()
            ->end()
        ;
    }
}
