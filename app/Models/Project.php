<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    public $oneOff = 1;
    public $installmental = 2;
    public $cardPayment = 1;
    public $transfer = 2;
    public function installations(){
        return $this->hasMany(ProjectOrder::class,'project_id');
    }
    public function payments(){
        return $this->hasMany(Project::class,"project_id","id");
    }
}

