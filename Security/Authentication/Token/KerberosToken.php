<?php

/*
 * This file is part of the RednoseKerberosBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\KerberosBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * The token handling the Kerberos authentication.
 */
class KerberosToken extends AbstractToken
{
    protected $providerKey;

    /**
     * Constructor.
     *
     * @param mixed $user        The user object.
     * @param mixed $providerKey The provider key.
     * @param array $roles       The roles to assign.
     */
    public function __construct($user, $providerKey, array $roles = array())
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

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }
}
