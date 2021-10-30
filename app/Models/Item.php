<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

     public function Subitem()
     {
         return $this->hasMany(Subitem::class);
     }

    public function Stockaddition()
    {
        return $this->hasMany(Stockaddition::class);
    }
}
