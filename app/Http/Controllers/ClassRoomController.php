<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Exception;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ClassRoom::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'capacity' => ['required', 'integer'],
        ]);

        $classroom = ClassRoom::create($data);

        return [
            'message' => 'Classroom Created',
            'status' => 200,
            'classroom' => $classroom
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $classroom = ClassRoom::findOrFail((int)$id);
            
            return [
                'status' => 200,
                'classroom' => $classroom,
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'Classroom not found',
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
            'capacity' => ['nullable', 'integer']
        ]);

        try {
            $classroom = ClassRoom::findOrFail((int)$id);
            $data['id'] = $classroom->id;

            ClassRoom::update($data);

            return [
                'message' => 'ClassRoom Created',
                'status' => 200,
                'classroom' => ClassRoom::find((int)$id)
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'ClassRoom not found',
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
            
            ClassRoom::destroy((int)$id);
            return [
                'message' => 'ClassRoom Deleted',
                'status' => 200,
            ];
        }
        catch(Exception $e){
            return [
                'message' => 'ClassRoom not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
