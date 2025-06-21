<?php

namespace App\Http\Controllers;

use App\Models\AcademicTrack;
use Exception;
use Illuminate\Http\Request;

class AcademicTrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AcademicTrack::with('level')->get()->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'studentsNumber' => ['required', 'integer', 'min:1'],
            'level_id' => ['required', 'exists:levels,id'],
            'classroom_id' => ['nullable', 'exists:class_rooms,id']
        ]);

        $academicTrack = AcademicTrack::create($data);

        return [
            'message' => 'Academic Track created',
            'status' => 201,
            'academic_track' => $academicTrack->load(['level', 'preferedClassRoom'])
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {

        $withSubjects = $request->query('with_subjects');
        try {
            $academicTrack = AcademicTrack::findOrFail($id);
            if ($withSubjects == '1') {
                $academicTrack->load(['level', 'preferedClassRoom', 'subjects']);
            }
            return $academicTrack->load(['level', 'preferedClassRoom']);
        } catch (Exception $e) {
            return [
                'message' => 'Academic Track not found',
                'status' => 404,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string'],
            'studentsNumber' => ['nullable', 'integer', 'min:1'],
            'level_id' => ['nullable', 'exists:levels,id'],
            'classroom_id' => ['nullable', 'exists:class_rooms,id']
        ]);

        try {
            $academicTrack = AcademicTrack::findOrFail($id);
            $academicTrack->update($data);
            return [
                'message' => 'Academic Track updated',
                'status' => 200,
                'academic_track' => $academicTrack->load(['level', 'preferedClassRoom'])
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Academic Track not found',
                'status' => 404,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $academicTrack = AcademicTrack::findOrFail($id);
            $academicTrack->delete();
            return [
                'message' => 'Academic Track deleted',
                'status' => 200
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Academic Track not found',
                'status' => 404,
                'error' => $e->getMessage()
            ];
        }
    }
}
