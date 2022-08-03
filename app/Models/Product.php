<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function invertertype(){
        return $this->hasOne(Subitem::class,"id","inverter_type");
    }
    public function paneltype(){
        return $this->hasOne(Subitem::class,"id","panel_type");
    }
    public function batteriestype(){
        return $this->hasOne(Subitem::class,"id","batteries_type");
    }
    
    public function accessories(){
        return $this->hasMany(Accessories::class,"product_id","id");
    }
}
