<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Admin;

class Admin_log extends Model{

  
public function admin(){


    return $this->belongsTo(Admin::class);

 //return Admin::where('id',$this->admin_id)->first()->email;

} 


}
