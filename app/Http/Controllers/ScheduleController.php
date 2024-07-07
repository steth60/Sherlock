<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instance;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function store(Request $request, Instance $instance)
    {
        $data = $request->validate([
            'action' => 'required|string',
            'months' => 'required|array',
            'days' => 'required|array',
            'hours' => 'required|array',
            'minutes' => 'required|array',
            'description' => 'nullable|string',
            'enabled' => 'required|boolean',
        ]);

        $data['instance_id'] = $instance->id;

        Schedule::create($data);

        return response()->json(['status' => 'success', 'message' => 'Schedule created successfully.']);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'action' => 'required|string',
            'months' => 'required|array',
            'days' => 'required|array',
            'hours' => 'required|array',
            'minutes' => 'required|array',
            'description' => 'nullable|string',
            'enabled' => 'required|boolean',
        ]);

        $schedule->update($data);

        return response()->json(['status' => 'success', 'message' => 'Schedule updated successfully.']);
    }

    public function edit(Schedule $schedule)
    {
        return response()->json(['schedule' => $schedule]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Schedule deleted successfully.']);
    }

    public function triggerNow(Schedule $schedule)
    {
        // Your logic to trigger the schedule task immediately
        return response()->json(['status' => 'success', 'message' => 'Scheduled task triggered successfully.']);
    }
}
