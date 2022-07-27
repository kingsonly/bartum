<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOrderDetails extends Model
{
    use HasFactory;
    public $table = 'project_order_details';

    public function product(){
        return $this->hasMany(Stockaddition::class,'id', 'product_id');
    }
}
