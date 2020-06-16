<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class User extends Model{

  protected $fillable = ['username', 'profile_name','profile_photo','birthdate','status'];
  public $timestamps = false;

}
