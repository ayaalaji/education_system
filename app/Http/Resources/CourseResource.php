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
            'Title' => $this->title,
            'Description' => $this->description,
            'Start Register Date' => $this->start_register_date,
            'End Register Date' => $this->end_register_date,
            'Course Duration'  => $this->course_duration,
            'Start Date' => $this->start_date,
            'End Date' => $this->end_date,
            'Status' => $this->status,
            'Teacher' => $this->teacher->name,
            'Category' =>$this->category->name
        ];

             
        
    }
}
