<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = ['title', 'description', 'start_register_date', 'end_register_date', 'start_date', 
    'end_date', 'status','course_duration','category_id', 'teacher_id'];


    //---------------------------Relation---------------------------------------------------

    public function users()
    {

        return $this->belongsToMany(User::class)
                    ->withTimestamps();

    }

    //.....................

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    //....................

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

  //----------------------------Scope----------------------------------------------------
  
  public function scopeByFilter($query ,$teacher = null, $status = null,
                                $category = null, $start_date = null, $end_date = null,
                                $teacher_ids= [],$category_ids =[])
  {
   /*
   if the filters are null , no condection will add to the query and it will back with all the course:
    select * from courses
   */
   return $query->when($teacher && !empty($teacher_ids), function ($q) use ($teacher_ids) {
                    $q->whereIn('teacher_id', $teacher_ids);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($category && !empty($category_ids), function ($q) use ( $category_ids) {
                $q->whereIn('category_id', $category_ids);
            })
            ->when($start_date, function ($q) use ($start_date) {
                $q->where('start_date', '>=', $start_date);
            })
            ->when($end_date, function ($q) use ($end_date) {
                $q->where('end_date', '<=', $end_date);
            });        
   
    }
    

}
