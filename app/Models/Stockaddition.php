<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockaddition extends Model
{
    use HasFactory;

    public function Item()
    {
        return $this->belongsTo(Item::class, 'itemid');
    }

    public function Subitem()
    {
        return $this->belongsTo(Subitem::class, 'subitemid');
    }

    public function Addedby()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
