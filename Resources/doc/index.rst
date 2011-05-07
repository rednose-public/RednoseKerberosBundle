Provides Kerberos authentication for your Symfony2 Project.

Installation
============

Add KerberosBundle to your vendor/bundles/ dir
----------------------------------------------

::

    $ svn co svn://docgen.rednose.nl/docflow/trunk/RednoseKerberosBundle/ vendor/bundles/Rednose/KerberosBundle

Add the Rednose namespace to your autoloader
--------------------------------------------

::

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Rednose' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

Add KerberosBundle to your application kernel
---------------------------------------------

::

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Rednose\KerberosBundle\RednoseKerberosBundle(),
            // ...
        );
    }

Security configuration
----------------------

::

    # app/config/security.yml
    factories:
        - "%kernel.root_dir%/../vendor/bundles/Rednose/KerberosBundle/Resources/config/security_factories.xml"

     firewalls:
         secured:
             pattern:   /secured/.*
             kerberos: true
