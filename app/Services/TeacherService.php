<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

class TeacherService
{
    /**
     * Get a list of teachers with caching.
     *
     * @return array
     * @throws HttpResponseException
     */
    public function listTeachers()
    {
        try {

            $teachers = cacheData('teachers_list', function () {
                return Teacher::select('name', 'email')->get();
            });

            return $teachers;
        } catch (Exception $e) {
            Log::error('Error getting all teachers: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teachers.'], 500));
        }
    }

    /**
     * Create a new teacher with role assignment and caching.
     *
     * @param array $data
     * @return Teacher
     * @throws HttpResponseException
     */
    public function createTeacher(array $data)
    {
        try {
            $teacher = Teacher::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'specialization' => $data['specialization']
            ])->assignRole($data['role']);

            Cache::forget('teachers_list');
            return $teacher;
        } catch (Exception $e) {
            Log::error('Error creating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to create teacher.'], 500));
        }
    }

    /**
     * Get a specific teacher's data with caching.
     *
     * @param Teacher $teacher
     * @return Teacher
     * @throws HttpResponseException
     */
    public function getTeacher(Teacher $teacher)
    {
        try {
            return cacheData("teacher_{$teacher->id}", function () use ($teacher) {
                return $teacher;
            });
        } catch (Exception $e) {
            Log::error('Error getting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teacher.'], 500));
        }
    }

    /**
     * Update an existing teacher's data and clear cache.
     *
     * @param Teacher $teacher
     * @param array $data
     * @return Teacher
     * @throws HttpResponseException
     */
    public function updateTeacher(Teacher $teacher, array $data)
    {
        try {
            $teacher->update(array_filter($data));

            if (isset($data['role'])) {
                $teacher->syncRoles([$data['role']]);
            }

            Cache::forget("teacher_{$teacher->id}");
            Cache::forget('teachers_list');
            return $teacher;
        } catch (Exception $e) {
            Log::error('Error updating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to update teacher.'], 500));
        }
    }


    /**
     * Delete a teacher and clear cache.
     *
     * @param Teacher $teacher
     * @return void
     * @throws HttpResponseException
     */
    public function deleteTeacher(Teacher $teacher)
    {
        try {
            $teacher->delete();

            Cache::forget("teacher_{$teacher->id}");
            Cache::forget('teachers_list');
        } catch (Exception $e) {
            Log::error('Error deleting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete teacher.'], 500));
        }
    }


}
