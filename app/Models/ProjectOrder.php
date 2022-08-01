<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOrder extends Model
{
    use HasFactory;
    public $table = 'project_order'; 

    public function orderaddress(){
        return $this->hasOne(ProjectAddress::class,'id');
    }

    public function orderdetails(){
        return $this->hasMany(ProjectOrderDetails::class,'order_id');
    } 
    

    
}
