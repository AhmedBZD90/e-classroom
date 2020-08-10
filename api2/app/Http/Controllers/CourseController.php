<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
         $this->middleware('permission:course-list|course-create|course-edit|course-delete', ['only' => ['index','show']]);
         $this->middleware('permission:course-create', ['only' => ['create','store']]);
         $this->middleware('permission:course-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:course-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (\Request::is('api*')) {

            $courses = DB::table('courses')
                ->join('users','courses.teacher_id','=','users.id')
                ->select('courses.*',
                         'users.name as teacher_name')
                ->get();
    
            
           return response()->json($courses, 200);
           
        }else{

            $courses = Course::latest()->paginate(5);
            return view('courses.index',compact('courses'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
        }
   
    }


    public function teachers()
    {
        if (\Request::is('api*')) {

             $teachers = DB::table('users')
                ->join('model_has_roles','model_has_roles.model_id','=','users.id')
                ->where('model_has_roles.role_id', 2)
                ->select('users.name','users.id')->get();
    
            
           return response()->json($teachers, 200);
           
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        

        $teachers = DB::table('users')
                ->join('model_has_roles','model_has_roles.model_id','=','users.id')
                ->where('model_has_roles.role_id', 5)
                ->select('users.name','users.id')
                ->pluck('name','id');


        return view('courses.create',compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $name = $request['name'];
        $description = $request['description'];
        

        if (\Request::is('api*')) {
            $teacher_id = $request['teacher_id'];
            $started_at = $request['started_at'];
            $finished_at = $request['finished_at'];
            $status = $request['status'];

            $course = Course::create([
                'name' => $name,
                'description' => $description,
                'teacher_id' => $teacher_id,
                'started_at' => $started_at,
                'finished_at' => $finished_at,
                'status' => $status
            ]);
            return response()->json([
                'message' => 'Great success! New Course created',
                'course' => $course
            ]);

        } else {

            $teacher_id = $request->input('teachers')[0];

            $course = Course::create([
                'name' => $name,
                'description' => $description,
                'teacher_id' => $teacher_id
            ]);
            return redirect()->route('courses.index')
                        ->with('success','Course created successfully.');
        }
            
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        // Searching for the teacher name releated to this course
        $teacher_name = DB::table('users')
                            ->where('users.id', $course->teacher_id)
                            ->select('users.name')
                            ->pluck('name');

        // Generating the Qr Code from course_id                    
        $qr_code = (string) $course->id;

        if (\Request::is('api*')) {     
            $response = [
                'Course' => $course,
                'teacher_name' => $teacher_name,
                'qr_code' => $qr_code
            ];
            
            return response()->json($response, 200);
        } else {
            return view('courses.show',compact('course','teacher_name', 'qr_code'));
        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        $teachers = DB::table('users')
                ->join('model_has_roles','model_has_roles.model_id','=','users.id')
                ->where('model_has_roles.role_id', 5)
                ->select('users.name','users.id')
                ->pluck('name','id');

        return view('courses.edit',compact('course','teachers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $name = $request['name'];
        $description = $request['description'];

        if (\Request::is('api*')) {

            $teacher_id = $request['teacher_id'];

            $course->update([
                'name' => $name,
                'description' => $description,
                'teacher_id' => $teacher_id
            ]);
            return response()->json([
                'message' => 'Great success! Course updated successfully',
                'course' => $course
            ]);

        } else {
            $teacher_id = $request->input('teachers')[0];

            $course->update([
                'name' => $name,
                'description' => $description,
                'teacher_id' => $teacher_id
            ]);

            return redirect()->route('courses.index')->with('success','Course updated successfully');
        }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();

         if (\Request::is('api*')) {
             return response()->json([
                'message' => 'Great success! Course deleted successfully'
            ]);
         } else {
            return redirect()->route('courses.index')
                        ->with('success','Course deleted successfully');
         }  
    }

}
