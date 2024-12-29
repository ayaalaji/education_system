<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'start_register_date', 'end_register_date', 'start_date', 'end_date', 'status', 'teacher_id'];


    //---------------------------Relation---------------------------------------------------

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    //.....................

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    //.......................

    // public function categorys()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    //.......................

    // public function materials()
    // {
    //     return $this->hasMany(Material::class);
    // }

    //.......................

    // public function tasks()
    // {
    //     return $this->hasMany(task::class);
    // }
}
