<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
}
