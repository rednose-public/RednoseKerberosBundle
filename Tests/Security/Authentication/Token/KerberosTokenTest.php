<?php

/*
 * This file is part of the RednoseKerberosBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\KerberosBundle\Tests\Security\Authentication\Token;

use Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken;

class KerberosTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KerberosToken
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new KerberosToken($this->getUser(), 'testKey');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTokenWithoutUser()
    {
        new KerberosToken(null, 'test');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTokenWithoutProviderKey()
    {
        new KerberosToken($this->getUser(), null);
    }

    /**
     * @covers Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken::getCredentials
     */
    public function testGetCredentials()
    {
        $this->assertEquals(null, $this->object->getCredentials());
    }

    /**
     * @covers Rednose\KerberosBundle\Security\Authentication\Token\KerberosToken::getProviderKey
     */
    public function testGetProviderKey()
    {
        $this->assertEquals('testKey', $this->object->getProviderKey());
    }

    protected function getUser()
    {
        return new \Symfony\Component\Security\Core\User\User('username', 'password');
    }
}
