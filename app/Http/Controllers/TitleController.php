<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Exception;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Title::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $title = Title::create($data);

        return [
            'message' => 'Title Created',
            'status' => 201,
            'title' => $title
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string | int $id)
    {
        try {
            $title = Title::findOrFail((int)$id);

            return [
                'status' => 200,
                'title' => $title,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Title not found',
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
        ]);

        try {
            $title = Title::findOrFail((int)$id);
            $title->update($data);

            return [
                'message' => 'Title Updated',
                'status' => 200,
                'title' => $title->fresh()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Title not found',
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

            Title::destroy((int)$id);
            return [
                'message' => 'Title Deleted',
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Title not found',
                'status' => 422,
                'errors' => $e->getMessage()
            ];
        }
    }
}
