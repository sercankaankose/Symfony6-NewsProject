<?php

namespace App\Params;

use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator;

class RoleParams
{
  const ROLE_AUTHOR = 0;
  const ROLE_EDITOR = 1;
}


