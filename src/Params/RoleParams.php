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

class NotificationParams
{
  const NOT_NEWADD = 'New News Added';
  const NOT_EDITREQUEST = 'News submitted for editing';
  const NOT_PUBLISHED = 'News Published';
  const NOT_SENDFOREDIT = 'Sent for news editing';
  const NOT_TIMEGIVENFOREDIT = 'Time was given to edit the news';
  const NOT_NEWSEDITED = 'The news has been corrected';


}
