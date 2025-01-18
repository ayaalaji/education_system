<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

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
     * Mutator to ensure the first letter of the name is capitalized.
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * Mutator to hash the password.
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed // Use mixed for return type
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // Relationships
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, "course_user")
            ->withTimestamps()
            ->withPivot('deleted_at');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user', 'student_id', 'task_id')
            ->withTimestamps()
            ->withPivot('file_path', 'summation_date', 'note', 'grade', 'deleted_at');
    }



    /**
     * When soft deleting User, also soft delete related course and task entries in pivot tables.
     * Detach relationships if force deleting
     */
    protected static function boot()
    {
        parent::boot();


        static::deleting(function ($user) {
            if ($user->isForceDeleting()) {
                $user->courses()->detach();
                $user->tasks()->detach();
            } else { // Soft delete related pivot table entries
                $user->courses()->updateExistingPivot($user->courses()->allRelatedIds(), ['deleted_at' => now()]);
                $user->tasks()->updateExistingPivot($user->tasks()->allRelatedIds(), ['deleted_at' => now()]);

            }
        });



    }
}
