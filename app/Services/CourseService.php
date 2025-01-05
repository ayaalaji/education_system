<?php
namespace App\Services;

use App\Events\CourseRegistrationEvent;
use App\Jobs\SendCourseRegistrationEmail;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Faker\Extension\ExtensionNotFound;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseService
{
    /**
     *  list of Courses
     * @param mixed $teacher
     * @param mixed $status
     * @param mixed $category
     * @param mixed $start_date
     * @param mixed $end_date
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function listCourses($teacher, $status, $category, $start_date, $end_date)
    {
        try{
          
            /* $teacher_ids and $category_ids are for Avoid Repeated Database Hits "Repeated database hits" refer to multiple queries sent to the database to 
            * fetch related or necessary data. This can impact performance,
            * especially when those queries are executed within a loop or for every condition, 
            * leading to slower response times

            note: 
            i trans from collection(elquam collection) to array (toArray()) , to check if $category_ids, teacer_ids 
            are empty or not in same function, becouse empty() is use with attay but ->isEmpty() is use
            with collection.
            note:
            cache save the result of querry so it array.
            */
            

            $teacher_ids = $teacher ? 
            Cache::remember('teacher_ids_' . md5($teacher),60, function () use($teacher) 
            {
                return Teacher::where('name', 'LIKE', '%' . $teacher . '%')
                                ->pluck('id')->toArray() ; 
            }):[]; 
            
            
            $category_ids = $category ?
            Cache::remember('category_ids_' . md5($category),60, function () use($category) 
            {
                return Category::where('name', 'LIKE', '%' . $category . '%')
                                ->pluck('id')->toArray();
            }) : [];
           
             
            /*
            *throw an error if the name of category or teacher not found
            */

            if ( (empty($teacher_ids) && $teacher )||(empty($category_ids) && $category) ) 
            {
                return false;
            }
        
           // uniqe cache key
            $cacheKey = 'courses_filter_' . md5(serialize([
                'teacher' => $teacher,
                'status' => $status,
                'category' => $category,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'teacher_ids' => $teacher_ids,
                'category_ids' => $category_ids,
            ]));
          
            $courses = Cache::remember($cacheKey ,60,
            function()use($teacher, $status, $category,$start_date, $end_date,$teacher_ids,$category_ids)
            {
                return Course::byFilter($teacher, $status, $category,
                                   $start_date, $end_date,
                                  $teacher_ids,$category_ids)->get();
            });
       
           //dd($courses);
         return $courses;
        
        } catch (Exception $e) {
            Log::error('Error getting all courses: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve teachers.'.$e->getMessage()], 500));
         } 

    }

    //...............................................................................................
    //...............................................................................................
    /**
     * Store a course in the database
     * @param mixed $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return bool|Course|\Illuminate\Database\Eloquent\Model
     */
    public function storeCourse($data)
    {
        try{
            DB::beginTransaction();
            
            //check if the course name is allready excist 9the whole name the same)
            $title = course::where('title','LIKE', $data['title'] )->count();
            
            if($title > 0)
            {
                return false;
            }else{
                $course = Course::create([
                    'title'           => $data['title'],
                    'description'     => $data['description'],
                    'course_duration' => $data['course_duration'],
                    'category_id'     => $data['category_id'],
                    'teacher_id'      => $data['teacher_id']
                ]);

                Cache::forget('courses_filter');
                Cache::forget('titcher_id');
                Cache::forget('category_id');

                DB::commit();
               
                return $course;
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error while store a course : ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed in the server'.$e->getMessage()], 500));
        }
    }

    //...............................................................................................
    //...............................................................................................
    /**
     * update a course title , description and category
     * @param mixed $course
     * @param mixed $validation_data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function updateCourse($course, $validation_data)
    {
        try {
            DB::beginTransaction();

            $course['title'] = $validation_data['title']?? $course->title;
            $course['description'] = $validation_data['description']?? $course->description;
            $course['course_duration'] = $validation_data['course_duration']?? $course['course_duration'];
            $course['category_id'] = $validation_data['category_id']?? $course['category_id'];
            $course->save();
     
            Cache::forget('courses_filter');
            Cache::forget('titcher_id');
            Cache::forget('category_id');
            
            DB::commit();

             return $course;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error updating the course ' . $e->getMessage());
                throw new HttpResponseException(response()->json(['message' => 'Failed in the server.'], 500));
            }
    }

    //...............................................................................................
    //...............................................................................................
    /**
     * Delte a course by its id
     * @param mixed $course
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return bool
     */
    public function deleteCourse($course)
    {
        try {
            $course->delete();
            Cache::forget('courses_filter');
            Cache::forget('titcher_id');
            Cache::forget('category_id');
            return true;
        } catch (Exception $e) {
            Log::error('Error while Deliting  the course ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed in the server.'.$e->getMessage()], 500));
        }

    }

//-------------------------------------------End OF CRUD FUNCTION------------------------------------------------------

//..........................................Soft Deltes................................................................
/**
 *force delete a course 
 * @param mixed $id
 * @throws \Exception
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return bool
 */
public function forceDeleteCourse($id)
{
    try {
            $arry_of_deleted_corses = Course::onlyTrashed()->pluck('id')->toArray();
            if(in_array($id,$arry_of_deleted_corses))
            {
                $course = Course::onlyTrashed()->find($id);
                $course->forceDelete();
                return true;
            }else{
                throw new Exception("This id is not Deleted yet,or dosn't exist!!");
            }


    } catch (Exception $e) {
        Log::error('Error while  Force Deliting  the course ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
    }

}

//................................................
//................................................
/**
 * Restore a Course
 * @param mixed $id
 * @throws \Exception
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return array|mixed|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
 */
public function restoreCorse($id)
{
    try {
         $course = Course::onlyTrashed()->find($id);

         if(is_null($course))
         {
            throw new Exception("This id is not Deleted yet,or dosn't exist!!");
         }
         $course->restore();
         return $course;

    } catch (Exception $e) {
        Log::error('Error while  Restoring the course ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
    }

}
//...........................
//...........................
 /**
  * get All the Trashed Courses
  * @throws \Exception
  * @throws \Illuminate\Http\Exceptions\HttpResponseException
  * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
  */
 public function getAllTrashedCourses()
 {
    try {
        $courses = Course::onlyTrashed()->get();
        if($courses->isEmpty())
        {
            throw new Exception('There are no Deleted Corses');
        }
        return $courses;
    } catch (Exception $e) {
        Log::error('Error while  get all trashed courses ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
    }
 }

//.....................................................................................................................
//.....................................................................................................................
/**
 * Summary of updateStatus
 * @param mixed $data
 * @param mixed $course
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return mixed
 */
public function updateStatus($data, $course)
{
    try {
       DB::beginTransaction();

       $course->status = $data['status'];
       $course->save();
       Cache::forget('courses_filter');

       DB::commit();
       return $course;
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error while updating the course  Status ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server.'], 500));
    }

}

//............................................
//............................................


/**
 * Update start and End Date of the course
 * @param mixed $course
 * @param mixed $data
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return mixed
 */
public function updateCourseStartDate($course,$data)
{
    try {
        DB::beginTransaction();

        $course->start_date = $data['start_date'];

       Cache::forget('courses_filter');

       DB::commit();

       $course->save();
       return $course;
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error while updating the course  Start Date ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
    }
}

//............................................
//............................................

public function updateCourseEndDate($course,$data)
{
    try {
        DB::beginTransaction();
        if($course->start_date)
        {
            $start = Carbon::parse($course->start_date);
            $end   = Carbon::parse($data['end_date']);

            if(!$end->isAfter($start))
            {
                throw new Exception("END Date shoulde Be After Start Date: ".$start->toDateString()); 
            }

            $week = $start->diffInWeeks($end);
            $atLeast_end_date = $start->addWeeks($course->course_duration);

            if($week >= $course->course_duration)
            {
                $course->end_date = $data['end_date'];
                
            }else{
                throw new Exception('The end Date should be at least at :'.$atLeast_end_date->toDateString());
            } 
        }else{
            throw new Exception('The start Date should not be null .');
        }
        
        Cache::forget('courses_filter');

        DB::commit();
 
        $course->save();
        return $course;

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error while updating the course end Date ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server: '.$e->getMessage()], 500));
    }
}

//...........................................
//...........................................
/**
 * update start and end rigister date
 * @param mixed $data
 * @param mixed $course
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return mixed
 */
public function updateStartRegisterDate($data,$course)
{
    try {
        DB::beginTransaction();

       $course->start_register_date = $data['start_register_date'];

       Cache::forget('courses_filter');
       $course->save();

        DB::commit();

       return $course;

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error while updating the course  Start and end Register Date ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server.'], 500));
    }

}

//.......................................................
//.......................................................



public function updateEnRegisterdDate($data,$course)
{
    try {
        DB::beginTransaction();
        if(is_null($course->start_register_date))
        {
            throw new Exception("Start Register Date shoulde Not Be Null.");
        }
       
        $start = Carbon::parse($course->start_register_date);
        $end   = Carbon::parse( $data['end_register_date']);
        // dd($start->toDateString(),$end->toDateString(),$end->isAfter($start));
        if(!$end->isAfter($start))
        {
            throw new Exception("END Register Date shoulde Be After Start Date: ".$start->toDateString()); 
        }

        $course->end_register_date = $data['end_register_date'];

        Cache::forget('courses_filter');
    
        DB::commit();

        $course->save();
        return $course;
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error while updating the course end Register Date ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server: '.$e->getMessage()], 500));
    }
}
//................................................
//................................................

/**
 * Add array of users  to the pivot table
 * (course_student) to spicific course 
 * @param mixed $data
 * @param mixed $course
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return mixed
 */
public function addUserToCourse($data,$course)
{
    echo $data['user'];
        echo $course;
    try {
        DB::beginTransaction();

        // Validate the users input
        
        if (!isset($data['user']) || !is_numeric($data['user'])) {
            throw new InvalidArgumentException('Invalid user data.');
        }
        

        //use attach to add the user to the course
        $course->users()->attach($data['user']);
 
        DB::commit();
           
        // Return the course with updated users
        return $course->load('users');
        
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error adding users to course ID ' . $course->id . ': ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server.'], 500));
    }
}


}