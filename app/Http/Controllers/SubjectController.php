<?php

namespace App\Http\Controllers;

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
        $levelId = $request->query('level_id');
        $levelName = $request->query('level');
        $professorId = $request->query('professor_id');
        $professorName = $request->query('professor');

        $professor = null;
        $warnings = array();
        if ($professorId) {
            $professor = Professor::find((int)$professorId);
        } else if ($professorName) {
            $professor = Professor::whereRaw('name LIKE "%' . $professorName . '%" OR firstname LIKE "%' . $professorName . '%"')->first();

            if (! (isset($professor) && $professor !== null)) {
                $warnings[] = 'Professor with name or firstname: ' . $professorName . ' doesn\'t exists';
            }
        }

        if ($levelId) {
            $level = Level::find((int)$levelId);

            if (isset($level) && $level !== null) {
                if ((isset($professor) && $professor !== null)) {
                    return $level->subjects()->where('professor_id', $professor->id)->get()->all();
                }
                return $level->subjects()->get()->all();
            }
        }

        if ($levelName && $levelName !== "") {
            $level = Level::where('name', $levelName)->first();

            if (isset($level) && $level !== null) {
                if ((isset($professor) && $professor !== null)) {
                    return $level->subjects()->where('professor_id', $professor->id)->get()->all();
                }
                return $level->subjects()->get()->all();
            }
            $warnings[] = 'Level with name: ' . $levelName . ' doesn\'t exists';
        }

        return Subject::all();
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

    public function link(string | int $subjectId, string | int $levelId, Request $request)
    {
        try {
            $subject = Subject::findOrFail((int)$subjectId);
            $level = Level::findOrFail((int)$levelId);

            $subject->levels()->attach($level);

            $subject->save();

            return [
                'message' => 'subject ' . $subject->name . ' linked to level ' . $level->name,
                'status' => 201,
                'subject' => $subject,
                'level' => $level
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Unexcepted Error',
                'status' => 422,
            ];
        }
    }
}
