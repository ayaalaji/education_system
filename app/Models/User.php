<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles ,SoftDeletes;

    protected $guard = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Mutator to ensure the first letter of the name is capitalized before saving to the database.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }
    /**
     * Mutator to hash the password before saving to the database.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //..................Relation

    public function courses()
    {
        return $this->belongsToMany(Course::class,"course_user")
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivotNull('deleted_at');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class,'task_user', 'task_id', 'student_id')
                ->withTimestamps()
                ->withPivot('file_path', 'summation_date','note','grade')
                ->withPivot('deleted_at')
                ->wherePivotNull('deleted_at');
    }


    //........................................
        /**
     * when soft delete User ,also softdeleting  the user signed into course
     * from (course-user table), and their tasks (in task_user pivot table)
     * @return void
     */
    protected static function boot()
{
    parent::boot();

    static::deleting(function ($user) {
    // Detach courses from the user in the pivot table
    $user->courses()->detach();

    // Detach tasks from the user in the pivot table
    $user->tasks()->detach();
    });
}
}
