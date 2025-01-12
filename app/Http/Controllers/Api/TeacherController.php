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
     * Soft delete a teacher by their ID. This marks the teacher as deleted but doesn't permanently remove it.
     *
     * @param int $id The ID of the teacher to be soft deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function soft_delete($id)
    {
        $teacher = Teacher::findOrFail($id); 

        $teacher->delete(); 

        return $this->success('Teacher archived successfully.', 200);
    }

    /*
     * Permanently delete a teacher by their ID. This completely removes the teacher from the database.
     *
     * @param int $id The ID of the teacher to be permanently deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function force_delete($id)
    {
        Teacher::withTrashed()->where('id', $id)->forceDelete(); 

        return $this->success('Teacher deleted successfully.', 200);
    }

    /*
     * Restore a soft-deleted teacher by their ID.
     *
     * @param int $id The ID of the teacher to be restored.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        Teacher::withTrashed()->where('id', $id)->restore(); 

        return $this->success('Teacher restored successfully.', 200);
    }

}
