<?php

namespace App\Services;

use App\Models\Teacher;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    /**
     * Get a list of teachers.
     *
     * @return array An array containing the message, data (teachers), and status code.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function listTeachers()
    {
        try {
            $teachers = Teacher::all();
            if ($teachers->isEmpty()) { //Check if the collection is empty instead of null
                return [
                    'message' => 'There are no teachers yet.',
                    'data' => [], //Return an empty array instead of null
                    'status' => 200
                ];
            }

            return [
                'message' => 'Teachers list retrieved successfully.',
                'data' => $teachers,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error getting all teachers: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teachers.'], 500));
        }
    }

    /**
     * Create a new teacher with valid data.
     *
     * @param array $data The teacher data to create.
     * @return array An array containing the message, data (created teacher), and status code.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function createTeacher(array $data)
    {
        try {
            $teacher = Teacher::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'specialization' => $data['specialization']
            ]);
            return [
                'message' => 'Teacher created successfully.',
                'data' => $teacher,
                'status' => 201 // Use 201 (Created) status code
            ];
        } catch (Exception $e) {
            Log::error('Error creating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to create teacher.'], 500));
        }
    }

    /**
     * Get a specific teacher's data.
     *
     * @param Teacher $teacher The teacher model instance.
     * @return array An array containing the message, data (teacher), and status code.
     * @throws HttpResponseException If an error occurs. This is unlikely here, but good practice.
     */
    public function getTeacher(Teacher $teacher)
    {
        try {
            return [
                'message' => 'Teacher retrieved successfully.',
                'data' => $teacher,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error getting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teacher.'], 500));
        }
    }

    /**
     * Update an existing teacher's data.
     *
     * @param Teacher $teacher The teacher model instance.
     * @param array $data The data to update the teacher with.
     * @return array An array containing the message, data (updated teacher), and status code.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function updateTeacher(Teacher $teacher, array $data)
    {
        try {
            $teacher->update(array_filter($data)); //array_filter removes null values
            return [
                'message' => 'Teacher updated successfully.',
                'data' => $teacher,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error updating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to update teacher.'], 500));
        }
    }

    /**
     * Delete a teacher.
     *
     * @param Teacher $teacher The teacher model instance.
     * @return array An array containing the message and status code.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function deleteTeacher(Teacher $teacher)
    {
        try {
            $teacher->delete();
            return [
                'message' => 'Teacher deleted successfully.',
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error deleting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete teacher.'], 500));
        }
    }
}
