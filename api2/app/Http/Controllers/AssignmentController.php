<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Assignment;
use App\User;
use App\Course;
use Illuminate\Support\Facades\DB;
use DateTime;

class AssignmentController extends Controller
{
    public function store(Request $request)
    {
      $today = date('Y-m-d H:i:s');

      $this->validate($request, [
        'due_date' => 'required|date'/*|after:'.$today*/
      ]);
      $due_date = $request->input('due_date');
      $due_date = str_replace('T', ' ', $due_date);

      $due_date0 = preg_replace('/[^A-Za-z0-9\-]/', '', $due_date);
  

      $course_id = $request->input('course_id');
      $user_id = $request->input('user_id');
  
      $verif = DB::table('assignments')
                ->where('assignments.course_id', $course_id)
                ->where('assignments.user_id', $user_id)
                ->select('assignments.course_id','assignments.user_id');

      $course = DB::table('courses')
                    ->where('courses.id', $course_id)
                    ->select('courses.name')
                    ->pluck('name');

      $started_at = DB::table('courses')
                    ->where('courses.id', $course_id)
                    ->select('courses.started_at')
                    ->pluck('started_at');

      $started_at0 = preg_replace('/[^A-Za-z0-9\-]/', '', $started_at);

      /*$due_date1 = new DateTime($due_date0);
      $started_at1 = new DateTime($started_at0);

      $interval = $due_date1->diff($started_at1);
      $elapsed = $interval->format('%h Hours %i Minutes %s Seconds');*/

      $course_name = preg_replace('/[^A-Za-z0-9\-]/', '', $course);

      $count_verif = $verif->count();

      if($count_verif == 0){
        $assignment = Assignment::create([
          'due_date' => $due_date,
          'course_id' => $course_id,
          'user_id' => $user_id
        ]);
        /*$assignment = new Assignment;
        $assignment->due_date = $due_date;
        $assignment->course_id = $course_id;
        $assignment->user_id = $user_id;*/

        if ($assignment->save()) 
        {
          if (\Request::is('api*')) 
          {
            return response()->json(['message' => 'You are Now assigned to Course: '.$course_name
                                                  .' at '.$due_date], 200);

          } 
          else 
          {
              
                return redirect('/course/' . $id)->with('status', 'Assignment added successfully!');
          }   
        } 
        else 
        {
          return response()->json(['message' => 'There was an error !'], 500);
        }
      } else {
          return response()->json(['message' => 'You are already assigned to that course !'], 500);
      }
      
    }

    /**
     * Show details about a particular assignment
     * 
     * @param  integer $course_id 
     * @param  integer $user_id     
     * @param  integer $assignment_id 
     * @return Response response             
     */
    public function show($course_id, $user_id, $assignment_id)
    {
      $assignment = Assignment::find($assignment_id);
      $course = Course::find($course_id);
      $user = User::find($user_id);

      /* $course_name = $course->subject . ' ' . $course->course . '-' . $course->section;
      $course_instructor = $course->user_id; */

      return view('pages.course.assignment.show', [
        'course_name' => $course_name,
        'course_id' => $course_id,
        'user_id' => $user_id,
        'assignment' => $assignment,
        //'course_instructor' => $course_instructor,
        'due_date_formatted' => str_replace(' ', 'T', $assignment->due_date)
      ]);
    }

    /**
     * Delete a particular assignment
     * 
     * @param  integer     $course_id    
     * @param  integer    $assignment_id 
     * @return Response response
     */
    public function destroy($course_id, $assignment_id)
    {
      if (Assignment::destroy($assignment_id)) {
        return redirect('/course/' . $course_id)->with('status', 'Assignment deleted successfully!');
      }
    }

    public function update(Request $request, $course_id, $assignment_id)
    {
      $this->validate($request, [
        'title' => 'required',
        'due_date' => 'required|date',
        'description' => 'required'
      ]);

      $due_date = $request->input('due_date');
      $due_date = str_replace('T', ' ', $due_date);
      $due_date = $due_date . ':00';
      
      $assignment = Assignment::find($assignment_id);
      $assignment->title = $request->input('title');
      $assignment->due_date = $due_date;
      $assignment->description = $request->input('description');

      if ($assignment->save()) {
        return redirect('/course/' . $course_id . '/assignment/' . $assignment_id)->with('status', 'Assignment updated successfully!');
      }
    } 
    
    public function course_assignments($course_id){

      $assignments = DB::table('assignments')
                    ->join('users','users.id','=','assignments.user_id')
                    ->where('assignments.course_id', $course_id)
                    ->select('users.name','assignments.due_date')
                    ->get();

      $course = Course::find($course_id);

      return response()->json(['Course' => $course->name,
                              'Started_at' => $course->started_at,
                              'Assignments' => $assignments], 200);
    }

    public function user_assignments($user_id){

      $assignments = DB::table('assignments')
                    ->join('courses','courses.id','=','assignments.course_id')
                    ->where('assignments.user_id', $user_id)
                    ->select('courses.name','courses.started_at', 'assignments.due_date')
                    ->get();
                    

      $user = User::find($user_id);

      return response()->json(['User' => $user->name,
                              'Assignments' => $assignments], 200);

    }
}
