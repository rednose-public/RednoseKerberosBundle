<?php

/*
 * This file is part of the RednoseKerberosBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\KerberosBundle\Security\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken;

/**
 * Hooks into the Symfony firewall.
 */
class KerberosListener implements ListenerInterface
{
    protected $logger;

    protected $securityContext;

    protected $authenticationManager;

    protected $providerKey;

    protected $dispatcher;

    protected $userKey;

    protected $defaultUser;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface       $securityContext       The security context object.
     * @param AuthenticationManagerInterface $authenticationManager The authentication manager.
     * @param mixed                          $providerKey           The provider key.
     * @param mixed                          $userKey               The user key.
     * @param mixed                          $defaultUser           A default user, optional.
     * @param mixed                          $logger                Logger instance.
     * @param EventDispatcherInterface       $dispatcher            The event dispatcher.
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, $userKey, $defaultUser = null, $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->userKey = $userKey;
        $this->defaultUser = $defaultUser;
    }

    /**
     * {@inheritdoc}
     */
    public final function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $this->logger) {
            $this->logger->debug(sprintf(
                'Checking secure context token: %s',
                $this->securityContext->getToken()));
        }

        $user = $this->getTokenUser($request);

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof KerberosToken && $token->isAuthenticated() && strtolower($token->getUsername()) === strtolower($user)) {
                return;
            }
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Trying to pre-authenticate user "%s"', $user));
        }

        try {
            $token = $this->authenticationManager->authenticate(new KerberosToken($user, $this->providerKey));

            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Authentication success: %s', $token));
            }
            $this->securityContext->setToken($token);

            if (null !== $this->dispatcher) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
            }
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Cleared security context due to exception: %s', $failed->getMessage()));
            }
        }
    }

    protected function getTokenUser(Request $request)
    {
        if (null !== $this->defaultUser) {
            return $this->defaultUser;
        }

        if (!$request->server->has($this->userKey)) {
            throw new BadCredentialsException(sprintf('Kerberos key was not found: %s', $this->userKey));
        }

        return $request->server->get($this->userKey);
    }
}
