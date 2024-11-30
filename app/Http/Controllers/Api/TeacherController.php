<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $teacherService;

    /*
     * Constructor to inject the TeacherService.
     *
     * @param TeacherService $teacherService
     */
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /*
     * Display a listing of teachers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   try{
        $teachers = $this->teacherService->listTeachers();
        if ($teachers->isEmpty()) { //Check if the collection is empty instead of null
            return $this->success([],'There are no teachers yet.',200);
            }
        else 
            return $this->success($teachers,'Teachers list retrieved successfully.',200);
       }catch (Exception $e) {
            Log::error('Error getting all teachers: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teachers.'], 500));
        }
    }

    /*
     * Store a newly created teacher in storage.
     *
     * @param StoreTeacherRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTeacherRequest $request)
    {  try{
            $validatedData = $request->validated();
            $teacher = $this->teacherService->createTeacher($validatedData); 
            return $this->success($teacher,'Teacher created successfully.',201);
        }catch (Exception $e) {
            Log::error('Error creating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to create teacher.'], 500));
        }
    }

    /*
     * Display the specified teacher.
     *
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Teacher $teacher)
    {   try{
            $teacherData = $this->teacherService->getTeacher($teacher);
            return $this->success($teacherData, 'Teacher retrieved successfully.', 200);
       } catch (Exception $e) {
            Log::error('Error getting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teacher.'], 500));
       }
    }

    /*
     * Update the specified teacher in storage.
     *
     * @param UpdateTeacherRequest $request
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {  try{
            $validatedData = $request->validated();
            $teacher = $this->teacherService->updateTeacher($teacher, $validatedData);
            return $this->success($teacher, 'Teacher updated successfully.', 200);
       }catch (Exception $e) {
            Log::error('Error updating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to update teacher.'], 500));
       }
    }

    /*
     * Remove the specified teacher from storage.
     *
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Teacher $teacher)
    {   try{
            $result = $this->teacherService->deleteTeacher($teacher);
            return $this->success('Teacher deleted successfully.',200);
        } catch (Exception $e) {
            Log::error('Error deleting teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete teacher.'], 500));
        }
    }
}
