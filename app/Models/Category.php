<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'created_by'];

    public function coursies(){
        return $this->hasMany(Course::class);
    }
    public function teacher(){
        return $this->hasOne(Teacher::class);
    }
}
