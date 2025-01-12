<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskUser extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'task_user';

    protected $fillable =['course_id','user_id'];
}
