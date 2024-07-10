<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrustedDevice;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrustedDeviceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trustedDevices = $user->trustedDevices()->where('expires_at', '>', Carbon::now())->get();

        return view('settings.trusted-devices', compact('trustedDevices'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $deviceToken = Str::random(60);

        $trustedDevice = new TrustedDevice();
        $trustedDevice->user_id = $user->id;
        $trustedDevice->device_name = $request->input('device_name');
        $trustedDevice->device_token = $deviceToken;
        $trustedDevice->expires_at = Carbon::now()->addDays(90);
        $trustedDevice->save();

        return response()->json(['device_token' => $deviceToken]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->findOrFail($id);

        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        $trustedDevice->device_name = $request->input('device_name');
        $trustedDevice->save();

        return redirect()->route('settings.trusted-devices.index')->with('status', 'Trusted device updated successfully.');
    }

    public function renew($id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->findOrFail($id);

        $trustedDevice->expires_at = Carbon::now()->addDays(90);
        $trustedDevice->save();

        return redirect()->route('settings.trusted-devices.index')->with('status', 'Trusted device renewed successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->find($id);
        if ($trustedDevice) {
            $trustedDevice->delete();
        }

        return redirect()->route('settings.trusted-devices.index')->with('status', 'Trusted device removed successfully.');
    }
}
