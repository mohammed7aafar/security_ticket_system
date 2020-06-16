<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Blog_category extends Model{

  protected $fillable = ['category_name', 'category_icon'];
  public $timestamps = false;



  public function blog_post(){


    return $this->belongsTo(Blog_post::class);

 //return Admin::where('id',$this->admin_id)->first()->email;

} 


}
