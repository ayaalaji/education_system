<?php

namespace App\Models;

use App\Models\User; // Import the User model
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Teacher extends User implements JWTSubject
{
    /*
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = 'teacher';

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization' // Add specialization to fillable attributes
    ];

    /*
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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


}
