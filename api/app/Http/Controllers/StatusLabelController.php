<?php

namespace App\Http\Controllers;

use App\Models\StatusLabel;
use Illuminate\Http\Request;

class StatusLabelController extends Controller
{
    public function index()
    {
        return StatusLabel::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descriptions' => 'required|string',
            'new_status' => 'required|string',
            'camera_name' => 'required|string',
            'is_critical' => 'sometimes|boolean',
            'requires_replacement' => 'sometimes|boolean',
            'warranty_implication' => 'required|string',
        ]);

        $statusLabel = StatusLabel::create($validated);

        return response()->json($statusLabel, 201);
    }

    public function show(StatusLabel $statusLabel)
    {
        return $statusLabel;
    }

    // Add update() and destroy() methods here later if needed
}
