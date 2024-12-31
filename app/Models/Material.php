<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'file_path',
        'vedio_path',
        'course_id'
    ];
    public function cource(){
        return $this->belongsTo(Course::class);
    }
}
