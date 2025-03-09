<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Exception;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Timetable::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'author_id' => ['required', 'exists:users,id'],
            'weekOf' => ['required', 'date'],
        ]);

        $timetable = Timetable::create($data);

        return [
            'message' => 'Timetable Created',
            'status' => 201,
            'Timetable' => $timetable
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $timetable = Timetable::findOrFail((int)$id);

            return [
                'status' => 200,
                'Timetable' => $timetable->load('courses'),
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
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
            'weekOf' => ['nullable', 'date']
        ]);

        try {
            $timetable = Timetable::findOrFail((int)$id);
            $timetable->update($data);

            return [
                'message' => 'Timetable Updated',
                'status' => 200,
                'Timetable' => $timetable->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
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

            Timetable::destroy((int)$id);
            return [
                'message' => 'Timetable Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Timetable not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
