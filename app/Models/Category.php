<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
    use HasFactory ,SoftDeletes;
    protected $fillable = ['name', 'description','teacher_id'];

    public function courses(){
        return $this->hasMany(Course::class);
    }
    public function teacher(){
        return $this->hasOne(Teacher::class);
    }
}
