<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseUser extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'course_user';

    protected $fillable =[ 'file_path','summation_date','course_id','user_id','note','grade'];
   

}
