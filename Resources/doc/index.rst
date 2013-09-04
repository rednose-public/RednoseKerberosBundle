Provides Kerberos authentication for your Symfony2 Project.

Installation
============

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
    security:
        firewalls:
            secured:
                pattern:   /secured/.*
                kerberos: ~

                # Optional:
                kerberos:
                    default_user: admin (Custom user override)
                    user_key: REMOTE_USER (Default)
