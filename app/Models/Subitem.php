<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subitem extends Model
{
    use SoftDeletes;
    use HasFactory;

public function Item()
{
    return $this->belongsTo(Item::class, 'itemid');
}

public function Stockaddition()
{
    return $this->hasMany(Stockaddition::class);
}

}
