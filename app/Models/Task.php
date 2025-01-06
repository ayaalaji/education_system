<?php

namespace App\Models;

use App\Models\Course;
use App\Events\TaskSubmittedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = ['title','due_date','status','course_id'];
    /**
     * Summary of casts
     * @var array
     */
    protected $casts = ['due_date' => 'datetime',
    'course_id' => 'integer'
];
/**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::updated(function ($task) {
            // Check if the pivot table has been updated (submission date added)
            if ($task->isDirty('submitted_at') && $task->submitted_at != null) {
                // Retrieve the student who submitted the task
                // Assuming that 'users' is the relationship that links tasks to students
                $student = $task->users()->wherePivot('task_id', $task->id)->first(); 

                // Trigger the TaskSubmittedEvent to notify the teacher
                event(new TaskSubmittedEvent($task, $student)); // Fire the event
            }
        });
    }
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
                ->withPivot('file_path', 'summation_date')
                ->withTimestamps();;
}
}
