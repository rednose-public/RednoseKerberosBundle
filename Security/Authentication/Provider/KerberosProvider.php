<?php

/*
 * This file is part of the RednoseKerberosBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\KerberosBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken;

/**
 * The authentication provider.
 */
class KerberosProvider implements AuthenticationProviderInterface
{
    protected $userProvider;

    protected $userChecker;

    protected $providerKey;

    /**
     * Constructor.
     *
     * @param UserProviderInterface $userProvider A UserProviderInterface instance
     * @param UserCheckerInterface  $userChecker  A UserCheckerInterface instance
     * @param string                $providerKey  The provider key
     */
    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey)
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        if (!$user = $token->getUser()) {
            throw new BadCredentialsException('No pre-authenticated principal found in request.');
        }

        $user = $this->userProvider->loadUserByUsername($user);

        $this->userChecker->checkPostAuth($user);

        $authenticatedToken = new KerberosToken($user, $this->providerKey, $user->getRoles());
        $authenticatedToken->setAttributes($token->getAttributes());

        return $authenticatedToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof KerberosToken && $this->providerKey === $token->getProviderKey();
    }
}
