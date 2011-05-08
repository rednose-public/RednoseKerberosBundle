<?php

namespace Rednose\KerberosBundle\Security\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Events;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken;

class KerberosListener implements ListenerInterface
{
    protected $logger;
    private $securityContext;
    private $authenticationManager;
    private $providerKey;
    private $dispatcher;
    private $userKey;
    private $defaultUser;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, $userKey, $defaultUser = null,  $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;

        $this->userKey = $userKey;
        $this->defaultUser = $defaultUser;
    }

    public final function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Checking secure context token: %s', $this->securityContext->getToken()));
        }

        $user = $this->getTokenUser($request);

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof KerberosToken && $token->isAuthenticated() && $token->getUsername() === $user) {
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
                $this->dispatcher->dispatch(Events::onSecurityInteractiveLogin, $loginEvent);
            }
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->debug(sprintf("Cleared security context due to exception: %s", $failed->getMessage()));
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

	$user = explode('@', $request->server->get($this->userKey));

        return $user[0];
    }
}
