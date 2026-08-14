<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authsign;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Authsign::find(session('authsign_id'));
        return view('authsign.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Authsign::find(session('authsign_id'));

        $request->validate([
            'name'  => 'required|min:3',
            'email' => 'required|email|unique:authsigns,email,' . $user->id,
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        session(['authsign_name' => $user->name]);

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Profile updated',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function passwordEdit()
    {
        return view('authsign.profile.password');
    }

    public function passwordUpdate(Request $request)
    {
        $user = Authsign::find(session('authsign_id'));

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Password changed',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
