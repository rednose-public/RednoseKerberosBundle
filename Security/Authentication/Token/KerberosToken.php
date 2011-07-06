<?php

namespace Rednose\KerberosBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class KerberosToken extends AbstractToken
{
    protected $providerKey;

    public function __construct($user, $providerKey = null, array $roles = array())
    {
        parent::__construct($roles);

        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->setUser($user);
        $this->providerKey = $providerKey;

        if ($roles) {
            $this->setAuthenticated(true);
        }
    }

    public function getCredentials()
    {
        return null;
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }
}
