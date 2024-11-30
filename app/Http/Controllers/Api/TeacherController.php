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

    {   
        $teachers = $this->teacherService->listTeachers();
        if (!$teachers) {
            return $this->error('Getting teachers failed');
        }
        if (empty($teachers)) {
            return $this->success(null, 'there is no teacher yet', 200);
        }
        else 
            return $this->success($teachers,'Teachers list retrieved successfully.',200);
    }

    /*
     * Store a newly created teacher in storage.
     *
     * @param StoreTeacherRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTeacherRequest $request)
    {  
        $validatedData = $request->validated();
        $teacher = $this->teacherService->createTeacher($validatedData);
        if (!$teacher) {
            return $this->error('Creating Teacher faild');
        } 
        return $this->success($teacher,'Teacher created successfully.',201);  

    }

    /*
     * Display the specified teacher.
     *
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Teacher $teacher)
    {   
            $teacherData = $this->teacherService->getTeacher($teacher);
            if (!$teacherData) {
                return $this->error('Retrieving Teacher faild');
            } 
            return $this->success($teacherData, 'Teacher retrieved successfully.', 200);
    }

    /*
     * Update the specified teacher in storage.
     *
     * @param UpdateTeacherRequest $request
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {  
        $validatedData = $request->validated();
        $teacher = $this->teacherService->updateTeacher($teacher, $validatedData);
        if (!$teacher) {
            return $this->error('Updating Teacher faild');
        } 
        return $this->success($teacher, 'Teacher updated successfully.', 200);

    }

    /*
     * Remove the specified teacher from storage.
     *
     * @param Teacher $teacher
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Teacher $teacher)
    {   
        $result = $this->teacherService->deleteTeacher($teacher);
        return $this->success('Teacher deleted successfully.',200);

    }
}
