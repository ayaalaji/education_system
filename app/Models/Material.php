<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=[
        'title',
        'file_path',
        'video_path',
        'course_id'
    ];
    //The relationship between cource and materials
    public function course()
{
    return $this->belongsTo(Course::class, 'course_id');
}

}
