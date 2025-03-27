<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Exception;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $withSubject = $request->query('withSubject');

        if((int)$withSubject === 1) return Professor::with('subjects')->get()->all();
        return Professor::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'firstname' => ['required', 'string'],
            'title_id' => ['nullable', 'exists:titles,id']
        ]);

        $professor = Professor::create($data);

        return [
            'message' => 'Professor Created',
            'status' => 200,
            'professor' => $professor
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $professor = Professor::findOrFail((int)$id);

            return [
                'status' => 200,
                'professor' => $professor->load('title')
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Professor not found',
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
            'firstname' => ['nullable', 'string'],
            'title_id' => ['nullable', 'exists:titles,id']
        ]);

        try {
            $professor = Professor::findOrFail((int)$id);
            $professor->update($data);

            return [
                'message' => 'Professor Updated',
                'status' => 200,
                'professor' => $professor->fresh()->load('title')
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Professor not found',
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

            Professor::destroy((int)$id);
            return [
                'message' => 'Professor Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Professor not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
