<?php

/*
 * This file is part of the LibbitKerberosBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Libbit\KerberosBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Libbit\KerberosBundle\DependencyInjection\Security\Factory\KerberosFactory;

/**
 * Main bundle class.
 *
 * @author Marc Bontje <marc@rednose.nl>
 */
class LibbitKerberosBundle extends Bundle
{
      public function build(ContainerBuilder $container)
      {
          parent::build($container);

          $extension = $container->getExtension('security');
          $extension->addSecurityListenerFactory(new KerberosFactory());
      }
}
