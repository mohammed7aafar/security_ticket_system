<?php

namespace App\Domain\User\Data;

final class UserData
{
    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var string */
    public $salt;

    /** @var string */
    public $profile_name;

     /** @var string */
     public $profile_photo;

      /** @var string */
      public $birthdate;

       /** @var string */
     public $status;

          /** @var string */
          public $fcm_token;
}