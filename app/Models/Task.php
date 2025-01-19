<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;
    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = ['title','due_date','status','course_id','notes'];
    /**
     * Summary of casts
     * @var array
     */
    protected $casts = ['due_date' => 'datetime',
    'course_id' => 'integer'
];
/**
 * Summary of seTtitleAttribute
 * @param mixed $value
 * @return void
 */
public function seTtitleAttribute($value)
{
    $this->attributes['title'] = ucwords($value);
}

/**
 * Summary of setStatusAttribute
 * @param mixed $value
 * @return void
 */
public function setStatusAttribute($value)
{
    $this->attributes['status'] = ucwords($value);
}

/**
 * Summary of course
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function course()
{
    return $this->belongsTo(Course::class);
}

/**
 * Summary of users
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
public function users()
{
    return $this->belongsToMany(User::class,'task_user', 'task_id', 'student_id')
                ->withPivot('file_path', 'summation_date','note','grade')
                ->withTimestamps();;
}
}
