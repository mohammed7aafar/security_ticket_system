<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;

class Ticket extends Model{

  protected $fillable = ['category_id', 'user_id','type','previous_email','hack_date','personal_id','description','status'];
  public $timestamps = false;



   

}
