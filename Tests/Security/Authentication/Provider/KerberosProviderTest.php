<?php

namespace Rednose\KerberosBundle\Tests\Security\Authentication\Provider;

use Rednose\KerberosBundle\Security\Authentication\Provider\KerberosProvider;

class KerberosProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticate()
    {
        $userProvider = $this->getUserProvider();
        $userChecker = $this->getUserChecker();

        $provider = $this->getMock('Rednose\KerberosBundle\Security\Authentication\Provider\KerberosProvider', array('supports'), array($userProvider, $userChecker, 'kerberos'));

        $provider
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true));

        $token = $this->getToken();

        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('testuser'));

        $token
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));

        $user = $this->getUser();

        $userProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('testuser'))
            ->will($this->returnValue($user));

        $userChecker
            ->expects($this->once())
            ->method('checkPostAuth')
            ->with($this->equalTo($user));

        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array('ROLE_USER')));

        $authenticatedToken = $provider->authenticate($token);

        $this->assertTrue($authenticatedToken->isAuthenticated());
    }

    protected function getToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    protected function getUserProvider()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
    }

    protected function getUserChecker()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
    }

    protected function getUser()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }
}
