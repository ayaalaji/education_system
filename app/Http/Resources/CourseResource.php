<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Id' => $this->id,
            'Title' => $this->title,
            'Description' => $this->description,
            'Start_register_date' => $this->start_register_date,
            'End_register_date' => $this->end_register_date,
            'Course Duration'  => $this->course_duration,
            'Start_date' => $this->start_date,
            'End_date' => $this->end_date,
            'Status' => $this->status,
            'Teacher' => $this->teacher->name,
            'Category' =>$this->category->name

        ];

             
        
    }
}
