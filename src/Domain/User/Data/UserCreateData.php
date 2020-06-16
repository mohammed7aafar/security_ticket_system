<?php

namespace App\Domain\User\Data;

final class UserCreateData
{
    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string */
    public $salt;

    /** @var string */
    public $level;

     /** @var string */
     public $secret;
}