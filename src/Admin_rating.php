<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Admin_rating extends Model{


    protected $fillable = ['admin_id', 'ticket_id','rating','description','date'];

    public $timestamps = false;
}
