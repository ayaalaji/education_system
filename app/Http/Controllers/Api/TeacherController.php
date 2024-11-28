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
        return $this->success($teachers['data'], $teachers['message'], $teachers['status']);
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
        $teacher = $this->teacherService->createTeacher($validatedData); //Note: Method name should be createTeacher.
        return $this->success($teacher['data'], $teacher['message'], $teacher['status']);
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
        return $this->success($teacherData['data'], $teacherData['message'], $teacherData['status']);
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
        return $this->success($teacher['data'], $teacher['message'], $teacher['status']);
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
        return $this->success($result['message'], $result['status']);
    }
}
