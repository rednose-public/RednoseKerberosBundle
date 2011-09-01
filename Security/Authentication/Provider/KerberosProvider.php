<?php
/*
 * Copyright (c) 2011 RedNose <info@rednose.nl>
 * 
 * Licensed under the EUPL, Version 1.1 or - as soon they will be approved by
 * the European Commission - subsequent versions of the EUPL (the "Licence");
 * You may not use this work except in compliance with the Licence. You may
 * obtain a copy of the Licence at:
 * 
 * http://www.osor.eu/eupl
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the Licence is distributed on an "AS IS" basis, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * Licence for the specific language governing permissions and limitations
 * under the Licence.
 */

namespace Libbit\KerberosBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Libbit\KerberosBundle\Security\Authentication\Token\KerberosToken;

class KerberosProvider implements AuthenticationProviderInterface
{
    protected $userProvider;
    protected $userChecker;
    protected $providerKey;

    /**
     * Constructor.
     *
     * @param UserProviderInterface $userProvider
     *   A UserProviderInterface instance
     * @param UserCheckerInterface $userChecker
     *   A UserCheckerInterface instance
     * @param string $providerKey 
     *   The provider key
     */
    public function __construct(UserProviderInterface $userProvider,
                                UserCheckerInterface $userChecker,
                                $providerKey)
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
        if (!$this->supports($token))
        {
            return null;
        }

        if (!$user = $token->getUser())
        {
            throw new BadCredentialsException(
                'No pre-authenticated principal found in request.');
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
        return 
            $token instanceof KerberosToken &&
            $this->providerKey === $token->getProviderKey();
    }
}
