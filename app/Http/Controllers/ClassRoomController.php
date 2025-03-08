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
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $classroom = ClassRoom::create($data);

        return [
            'message' => 'Classroom Created',
            'status' => 201,
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
        } catch (Exception $e) {
            return [
                'message' => 'Classroom not found',
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
            'capacity' => ['nullable', 'integer', 'min:1']
        ]);

        try {
            $classroom = ClassRoom::findOrFail((int)$id);
            $classroom->update($data);

            return [
                'message' => 'Classroom Updated',
                'status' => 200,
                'classroom' => $classroom->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Classroom not found',
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

            ClassRoom::destroy((int)$id);
            return [
                'message' => 'ClassRoom Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'ClassRoom not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
