<?php

namespace App\Http\Controllers;

use App\Models\AcademicTrack;
use App\Models\Level;
use App\Models\Professor;
use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $academicTrackId = $request->query('academicTrackId');
        $professorId = $request->query('professor_id');

        $query = Subject::query();

        if ($academicTrackId !== null) {
            $query = $query->whereHas('academicTracks', function ($q) use ($academicTrackId) {
                $q->where('academic_tracks.id', $academicTrackId);
            });
        }

        if ($professorId !== null) {
            $query = $query->whereHas('professor', function ($q) use ($professorId) {
                $q->where('id', $professorId);
            });
        }

        return $query->get()->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'professor_id' => ['required', 'exists:professors,id'],
        ]);

        $Subject = Subject::create($data);

        return [
            'message' => 'Subject Created',
            'status' => 200,
            'Subject' => $Subject
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $subject = Subject::findOrFail((int)$id);

            return [
                'status' => 200,
                'subject' => $subject->load('professor'),
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Subject not found',
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
            'name' => ['nullable', 'string'],
            'professor_id' => ['nullable', 'exists:professors,id'],
        ]);

        try {
            $subject = Subject::findOrFail((int)$id);
            $subject->update($data);

            return [
                'message' => 'Subject Updated',
                'status' => 200,
                'subject' => $subject->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Subject not found',
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

            Subject::destroy((int)$id);
            return [
                'message' => 'Subject Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Subject not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }

    public function link(string | int $subjectId, string | int $academictrackId, Request $request)
    {
        try {
            $subject = Subject::findOrFail((int)$subjectId);
            $academicTrack = AcademicTrack::findOrFail((int)$academictrackId);

            $subject->academicTracks()->attach($academicTrack);

            $subject->save();

            return [
                'message' => 'subject ' . $subject->name . ' linked to academic track ' . $academicTrack->name,
                'status' => 201,
                'subject' => $subject,
                'academicTrack' => $academicTrack->load('level')
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Unexcepted Error',
                'status' => 422,
            ];
        }
    }
}
