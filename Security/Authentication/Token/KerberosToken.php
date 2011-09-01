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

namespace Libbit\KerberosBundle\Security\Authentication\Token;

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
