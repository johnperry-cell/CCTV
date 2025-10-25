<?php

namespace App\Http\Controllers;

use App\Models\StatusHistory;
use Illuminate\Http\Request;

class StatusHistoryController extends Controller
{
    public function index()
    {
        return StatusHistory::with(['camera', 'user', 'statusLabel'])->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'user_id' => 'required|exists:users,id',
            'status_label_id' => 'required|exists:status_labels,id',
            'reason' => 'required|string',
            'status_change_timestamp' => 'required|date',
            'new_status' => 'required|string',
            'urgency' => 'required|string',
        ]);

        $statusHistory = StatusHistory::create($validated);

        return response()->json($statusHistory->load(['camera', 'user', 'statusLabel']), 201);
    }

    public function show(StatusHistory $statusHistory)
    {
        return $statusHistory->load(['camera', 'user', 'statusLabel']);
    }

    // Add update() and destroy() methods here later if needed
}
