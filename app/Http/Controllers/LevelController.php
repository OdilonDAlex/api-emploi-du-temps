<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Exception;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Level::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'studentsNumber' => ['required', 'integer', 'min:1'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        $level = Level::create($data);

        return [
            'message' => 'Level Created',
            'status' => 201,
            'level' => $level->load('preferenceClassRoom')
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $level = Level::findOrFail((int)$id);

            return [
                'status' => 200,
                'level' => $level->load('preferenceClassRoom'),
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Level not found',
                'status' => 404,
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
            'name' => ['nullable', 'string', 'max:255'],
            'studentsNumber' => ['nullable', 'integer', 'min:1'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        try {
            $level = Level::findOrFail((int)$id);
            $level->update($data);

            return [
                'message' => 'Level Updated',
                'status' => 200,
                'level' => $level->fresh()->load('preferenceClassRoom')
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Level not found',
                'status' => 404,
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
            Level::destroy((int)$id);
            return [
                'message' => 'Level Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Level not found',
                'status' => 404,
                'errors' => $e->getMessage()
            ];
        }
    }
}
