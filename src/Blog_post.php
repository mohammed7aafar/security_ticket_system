<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Blog_post extends Model{

  protected $fillable = ['title', 'text','cover','date','views'];
  public $timestamps = false;



  public function admin(){


    return $this->belongsTo(Admin::class);

 //return Admin::where('id',$this->admin_id)->first()->email;

} 


}
