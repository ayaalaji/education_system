<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
