<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;

use App\Services\CourseService;
use App\Http\Controllers\Controller;

use App\Http\Resources\CourseResource;
use App\Http\Requests\Course\EndCourseRequest;

use App\Http\Requests\Course\StartCourseRequest;

use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\StatusCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Requests\Course\AddUserToCourseRequest;
use App\Http\Requests\Course\EndRegisterCourseRequest;
use App\Http\Requests\Course\StartRegisterCourseRequest;



class CourseController extends Controller
{

    protected $courseService;

    public function __construct(CourseService $courseService)
    {
      //  $this->middleware('auth:teacher-api');
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
     {  
        
        $courses = $this->courseService->listCourses($request->input('teacher'), 
                                                        $request->input('status'),
                                                        $request->input('category'),
                                                        $request->input('start_date'),
                                                        $request->input('end_date')); 
       
        if (!$courses)
        {
            if ($request->input('teacher') ) {return $this->error('No teacher IDs found for the given teacher name.');}
            if ($request->input('category')) {return $this->error('No category IDs found for the given category name.');}   
        }
        
        return $this->success(CourseResource::collection($courses), "Fetch All Courses with the spicific filter Successfully", 200);
    }

    //.............................................................................


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        
        $data = $request->only(['title','description','course_duration','teacher_id','category_id']);
    
        $course = $this->courseService->storeCourse($data);

        if(!$course)
        {
            return $this->error('This Name is already excist',400);
        }
        
        return $this->success(CourseResource::make($course),'Store Course Successfully',200);

    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $this->success($course->load('users'),'Fetch Course Successfully',200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( UpdateCourseRequest $request, Course $course)
    {
       $validation_data = $request->only(['title','description','course_duration','category_id']);
       $new_course = $this->courseService->updateCourse( $course, $validation_data);
       return $this->success(CourseResource::make($new_course),'Update Course Successfully',200); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);
        return $this->success(null,'Delete Course Successfully',200);
    }

    //----------------------------------------End CRUD Function-----------------------------------------
    //--------------------------------------------------------------------------------------------------

    //........................................SoftDeletes..............................................

    /**
     * Force Delete the course
     */
    public function forceDeleteCourse(string $id)
    {
        $this->courseService->forceDeleteCourse( $id);

        return $this->success(null,'Force Deleted Course Successfully',200);
    }

    //...................................................................
    //...................................................................
    /**
     * Rstore a deleted course
     */
    public function restoreCourse(string $id)
    {
        $course = $this->courseService->restoreCorse( $id);

        return $this->success(CourseResource::make($course),'Restore Course Successfully',200); 
    }


    /**
     * get All Trashed courses
     */
    public function getAllTrashed()
    {
        $courses = $this->courseService->getAllTrashedCourses();

        return $this->success(CourseResource::collection($courses),'Get All Trashed Couses Successfully');
     }

    //.........................................End Of SoftDelte........................................
    /**
     * update the status of the course (open or closed)
     
     */
    public function updateStatus(StatusCourseRequest $request, Course $course)
    {
        $data = $request->validated();
       $courseNew = $this->courseService->updateStatus($data, $course);
        return $this->success(CourseResource::make($courseNew),'Update Status Successfully',200);
    }

    //.........................................................................
    //.........................................................................

    /**
     * update Start And End Date of the course 
     * 
     */
    public function updateStartDate(StartCourseRequest $request, Course $course)
    {
        $data = $request->only(['start_date']);
        $coursenew = $this->courseService->updateCourseStartDate($course,$data);
        return $this->success(CourseResource::make($coursenew),'Update Start Date Successfully',200);

    }
    //..........................................
    //...........................................
    
    public function updateEndDate(EndCourseRequest $request, Course $course)
    {
        $data = $request->only(['end_date']);
        $coursenew = $this->courseService->updateCourseEndDate($course,$data);
        return $this->success(CourseResource::make($coursenew),'Update End Date Successfully',200);

    }

    //.......................................................................
    //.......................................................................

    /**
     * update start and register date
     
     */
    public function updateStartRegisterDate(StartRegisterCourseRequest $request, Course $course)
    {
        $data = $request->only(['start_register_date']);
        $courseNew = $this->courseService->updateStartRegisterDate($data,$course);
        return $this->success(CourseResource::make($courseNew),'Update Start Register Date Successfully',200);
    }
    
    //.......................................
    //.......................................

    public function updateEndRegisterDate(EndRegisterCourseRequest $request, Course $course)
    {
        $data = $request->only(['end_register_date']);
        $courseNew = $this->courseService->updateEndRegisterdDate($data,$course);
        return $this->success(CourseResource::make($courseNew),'Update Start Register Date Successfully',200);
        
    }

    //.......................................................................
    //.......................................................................

    /**
     * Add users(student) to the spicific course by its id
     */
    public function addUser(AddUserToCourseRequest $request, Course $course)
    {
       $data = $request->only(['user']);
       $course = $this->courseService->addUserToCourse($data,$course);
       return $this->success($course,'Add User to course Successfully',200);
       
    }
    



}
