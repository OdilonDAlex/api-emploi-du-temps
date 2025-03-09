<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Course::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'duration' => ['nullable', 'integer'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'timetable_id' => ['required', 'exists:timetables,id']
        ]);

        $course = Course::create($data);

        return [
            'message' => 'course Created',
            'status' => 200,
            'course' => $course
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $course = Course::findOrFail((int)$id);

            return [
                'status' => 200,
                'Course' => $course->with('timetable'),
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Course not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string | int $id)
    {
        $data = $request->validate([
            'duration' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ]);

        try {
            $course = Course::findOrFail((int)$id);
            $course->update($data);

            return [
                'message' => 'Course Updated',
                'status' => 200,
                'course' => $course->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Course not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string | int $id)
    {
        try {

            Course::destroy((int)$id);
            return [
                'message' => 'Course Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Course not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
