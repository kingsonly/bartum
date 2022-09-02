<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMiscellaneous extends Model
{
    use HasFactory;
    public $table = 'project_miscellaneous';
    
    public function mainmiscallaneous(){
        return $this->hasOne( Miscellaneous::class,"id","miscellaneous_id");
    }
   
    
}
