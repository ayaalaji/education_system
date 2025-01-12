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
     * This method retrieves all teachers from the database and returns their names and emails.
     *
     * @return array An array containing the list of teachers.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function listTeachers()
    {
        try {
            // Fetch all teachers' names and emails from the database
            $teachers = Teacher::select('name', 'email')->get();

            return $teachers; // Return the list of teachers
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error getting all teachers: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teachers.'], 500));
        }
    }

    /**
     * Create a new teacher with valid data.
     *
     * This method creates a new teacher record in the database using the provided data.
     *
     * @param array $data The teacher data to create.
     * @return array An array containing the created teacher's data.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function createTeacher(array $data)
    {
        try {
            // Create a new teacher record with hashed password and assign a role
            $teacher = Teacher::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']), // Hash the password for security
                'specialization' => $data['specialization']
            ])->assignRole($data['role']); // Assign role to the teacher

            return $teacher; // Return the created teacher's data
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error creating teacher: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to create teacher.'], 500));
        }
    }

    /**
     * Get a specific teacher's data.
     *
     * This method retrieves data for a specific teacher based on the provided Teacher model instance.
     *
     * @param Teacher $teacher The teacher model instance.
     * @return array The teacher's data.
     * @throws HttpResponseException If an error occurs (unlikely here, but good practice).
     */
    public function getTeacher(Teacher $teacher)
    {
        try {
            return $teacher; // Return the teacher's data directly
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error getting teacher: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teacher.'], 500));
        }
    }

    /**
     * Update an existing teacher's data.
     *
     * This method updates the specified teacher's information in the database.
     *
     * @param Teacher $teacher The teacher model instance.
     * @param array $data The data to update the teacher with.
     * @return array An array containing the updated teacher's data.
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function updateTeacher(Teacher $teacher, array $data)
    {
        try {
                        // Update the teacher's data with non-null values only
            $teacher->update(array_filter($data)); // array_filter removes null values

            // If a role is provided, sync it with the teacher's roles
            if (isset($data['role'])) {
                $teacher->syncRoles([$data['role']]); // Sync roles for the teacher
            }

            return $teacher; // Return the updated teacher's data
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error updating teacher: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to update teacher.'], 500));
        }
    }

}
