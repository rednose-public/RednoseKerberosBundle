Provides Kerberos authentication for your Symfony2 Project.

Installation
============

Add KerberosBundle to your src/ dir
----------------------------------------------

::

    $ git clone gitolite@blowdryer.local:LibbitKerberosBundle src/Libbit/KerberosBundle


Add KerberosBundle to your application kernel
---------------------------------------------

::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Libbit\KerberosBundle\LibbitKerberosBundle(),
            // ...
        );
    }

Security configuration
----------------------

::

    # app/config/security.yml
    security:
        factories:
            - "%kernel.root_dir%/../src/Libbit/KerberosBundle/Resources/config/security_factories.xml"

        firewalls:
            secured:
                pattern:   /secured/.*
                kerberos: ~

                # Optional:
                kerberos:
                    default_user: admin (Custom user override)
                    user_key: REMOTE_USER (Default)
