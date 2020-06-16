<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Admin extends Model{

  protected $fillable = ['email', 'level'];
  public $timestamps = false;

}
