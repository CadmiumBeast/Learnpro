<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use PharIo\Manifest\Email;

class CourseController extends Controller
{
    public function index(){
        $courses = Course::all();
        return response()->json($courses);
    }

    public function show($id){
        $courses = Course::where('id', $id)->get();

        $ins_id = auth('sanctum')->user()->id;
        $entrollementStatus = Enrollment::where('course_id', $id)->where('user_id', $ins_id)->exists();

        return response()->json($courses);
    }
    public function checkEnrollment($id){
        $ins_id = auth('sanctum')->user()->id;
        $enrolledCourses = Enrollment::where('course_id', $id)->where('user_id', $ins_id)->exists();;

        return response()->json($enrolledCourses);
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'string|required',
            'description' => 'string|required',
            'category' => 'string|required',
        ]);

        $ins_id = auth('sanctum')->user()->id;
        $usertype = User::where('id', $ins_id)->value('role');

        if ($usertype == 'instructor') {
            Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'instructor_id' => $ins_id,
            ]);

            return response()->json(['message' => 'Course created successfully']);
        }
        else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
    public function enroll($id){
        $student_id = auth('sanctum')->user()->id;
        $usertype = User::where('id', $student_id)->value('role');

        if ($usertype == 'student') {
            Enrollment::create([
                'course_id' => $id,
                'user_id' => $student_id,
            ]);

            return response()->json(['message' => 'Enrolled Successfully']);
        }
        else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }


    }
    public function unenroll($id){
        $student_id = auth('sanctum')->user()->id;

        $student_enrollment = Enrollment::where('user_id', $student_id)->where('course_id', $id)->get();

        $usertype = User::where('id', $student_id)->value('role');

        if ($usertype == 'student'){
            $student_enrollment->each->delete();

            return response()->json(['message' => 'Unenrolled Successfully']);
        }
        else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }

    }

}
