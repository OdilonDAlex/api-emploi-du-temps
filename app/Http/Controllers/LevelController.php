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
            'name' => ['required', 'string'],
            'studentsNumber' => ['required', 'integer'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        $level = Level::create($data);

        return [
            'message' => 'Level Created',
            'status' => 200,
            'Level' => $level->with('preferenceClassRoom')
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
                'Level' => $level,
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'Level not found',
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
            'studentsNumber' => ['nullable', 'integer'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        try {
            $level = Level::findOrFail((int)$id);
            $data['id'] = $level->id;

            Level::update($data);

            return [
                'message' => 'Level Created',
                'status' => 200,
                'Level' => Level::find((int)$id)
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'Level not found',
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
            
            Level::destroy((int)$id);
            return [
                'message' => 'Level Deleted',
                'status' => 200,
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'Level not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
