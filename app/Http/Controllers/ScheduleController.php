<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function create(Instance $instance)
    {
        return view('schedules.create', compact('instance'));
    }

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

        return redirect()->route('instances.show', $instance)->with('success', 'Schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        return view('schedules.edit', compact('schedule'));
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

        return redirect()->route('instances.show', $schedule->instance)->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('instances.show', $schedule->instance)->with('success', 'Schedule deleted successfully.');
    }
    public function triggerNow(Schedule $schedule)
    {
        $instance = $schedule->instance;
        $action = $schedule->action;

        try {
            $instanceController = new InstanceController();

            // Notify the user that the task is starting
            echo "<script>toastr.info('Starting scheduled task: $action');</script>";

            switch ($action) {
                case 'start':
                    $response = $instanceController->start(new Request(), $instance);
                    break;
                case 'stop':
                    $response = $instanceController->stop(new Request(), $instance);
                    break;
                case 'restart':
                    $response = $instanceController->restart(new Request(), $instance);
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            if ($response->getStatusCode() === 200) {
                echo "<script>toastr.success('Scheduled task $action completed successfully');</script>";
                Log::info("Triggered $action for instance: {$instance->id}");
            } else {
                echo "<script>toastr.error('Scheduled task $action failed');</script>";
                Log::error("Failed to trigger $action for instance: {$instance->id}");
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Failed to trigger action for instance: {$instance->id}", ['message' => $e->getMessage()]);
            echo "<script>toastr.error('Scheduled task $action failed');</script>";
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
