<?php

namespace App\Http\Controllers;

use App\Models\RepairLog;
use Illuminate\Http\Request;

class RepairLogController extends Controller
{
    public function index()
    {
        // Use with() to load related models in one query
        return RepairLog::with(['camera', 'user'])->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'user_id' => 'required|exists:users,id',
            'is_repaired' => 'sometimes|boolean',
            'repair_start_timestamp' => 'nullable|date',
            'repair_end_timestamp' => 'nullable|date|after_or_equal:repair_start_timestamp',
        ]);

        $repairLog = RepairLog::create($validated);

        // Return the new log with its relationships loaded
        return response()->json($repairLog->load(['camera', 'user']), 201);
    }

    public function show(RepairLog $repairLog)
    {
        return $repairLog->load(['camera', 'user']);
    }

    // Add update() and destroy() methods here later if needed
}
