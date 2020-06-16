<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Ticket_response extends Model{

  protected $fillable = ['admin_id', 'user_id','ticket_id','response_text','response_date'];
  public $timestamps = false;

}
