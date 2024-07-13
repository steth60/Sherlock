<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Models\Group;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalPermissions = Permission::count();
        $totalGroups = Group::count();

        $unverifiedEmails = User::whereNull('email_verified_at')->count();
        $mfaNotEnabled = User::where('two_factor_enabled', 0)->count();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalPermissions', 
            'totalGroups', 
            'unverifiedEmails', 
            'mfaNotEnabled'
        ));
    }

    public function unverifiedEmailUsers()
    {
        $users = User::whereNull('email_verified_at')->get();
        return response()->json($users);
    }

    public function mfaNotEnabledUsers()
    {
        $users = User::where('two_factor_enabled', 0)->get();
        return response()->json($users);
    }
}
