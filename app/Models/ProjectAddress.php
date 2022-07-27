<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAddress extends Model
{
    use HasFactory;
    public $table = 'project_address';

    public function lga(){
        return $this->hasOne(Lga::class,'lgaid','lgas_id');
    }
    
    public function state(){
        return $this->hasOne(State::class,'stateid', 'states_id');
    }

}
