<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Camera::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'current_status' => 'required|string|max:255',
        ]);

        $camera = Camera::create($validated);

        return response()->json($camera, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Camera $camera)
    {
        return $camera;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Camera $camera)
    {
        $validated = $request->validate([
            'location' => 'sometimes|required|string|max:255',
            'current_status' => 'sometimes|required|string|max:255',
        ]);

        $camera->update($validated);

        return response()->json($camera);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Camera $camera)
    {
        $camera->delete();

        return response()->json(null, 204);
    }
}
