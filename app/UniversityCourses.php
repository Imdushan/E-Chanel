<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCourses extends Model
{
    use SoftDeletes;
    protected $table = 'university_courses';
}
