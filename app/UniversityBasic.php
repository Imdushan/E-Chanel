<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityBasic extends Model
{


    use SoftDeletes;
    protected $table = 'university_basic';

    public function UniversityCourses()
    {

        return $this->hasMany(UniversityCourses::class,'un_id','id');
    }
}
