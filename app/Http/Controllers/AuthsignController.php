<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authsign;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Signature;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthsignController extends Controller
{
    public function registerForm()
    {
        return view('authsign.register');
    }

    public function register(Request $request)
    {
        $request->validate(
            [
                'name'     => 'required|min:3',
                'email'    => 'required|email|unique:authsigns,email',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'name.required'     => 'Name is required',
                'name.min'          => 'Name must be at least 3 characters',
                'email.required'    => 'Email is required',
                'email.email'       => 'Enter a valid email address',
                'email.unique'      => 'This email is already registered',
                'password.required' => 'Password is required',
                'password.min'      => 'Password must be at least 6 characters',
                'password.confirmed'=> 'Password confirmation does not match',
            ]
        );

        $user = Authsign::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Registered',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return redirect()->route('login.form')
            ->with('success', 'Registration successful! Please login.');
    }

    public function loginForm()
    {
        return view('authsign.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email'    => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required'    => 'Email is required',
                'email.email'       => 'Enter a valid email',
                'password.required' => 'Password is required',
            ]
        );

        $user = Authsign::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not registered');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password');
        }

        $user->last_login_at = now();
        $user->save();

        session([
            'authsign_id'   => $user->id,
            'authsign_name' => $user->name,
            'authsign_is_admin' => $user->is_admin,
        ]);

        if ($request->filled('remember')) {
            session()->put('authsign_remember', true);
        }

        if (!$user->signature) {
            return redirect()->route('signature.form');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Login successful!');
    }

    public function forgotPasswordForm()
    {
        return view('authsign.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:authsigns,email',
        ]);

        $user = Authsign::where('email', $request->email)->first();
        $token = Str::random(60);

        return redirect()->route('password.reset', $token)
            ->with('email', $request->email);
    }

    public function resetPasswordForm($token)
    {
        return view('authsign.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:authsigns,email',
            'password'              => 'required|min:6|confirmed',
        ]);

        $user = Authsign::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login.form')
            ->with('success', 'Password reset successful! Please login.');
    }

    public function verifyEmail($id)
    {
        $user = Authsign::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('login.form')
            ->with('success', 'Email verified successfully! Please login.');
    }

    public function dashboard()
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user->signature) {
            return redirect()->route('signature.form');
        }

        $signatures = $user->signatures()->latest()->take(5)->get();
        $documents = $user->documents()->latest()->take(5)->get();
        $recentLogs = ActivityLog::where('authsign_id', $user->id)->latest()->take(5)->get();

        return view('authsign.dashboard', compact('user', 'signatures', 'documents', 'recentLogs'));
    }

    public function signatureForm()
    {
        return view('authsign.signature');
    }

    public function signatureSave(Request $request)
    {
        if (!$request->signature) {
            return back()->with('error', 'Signature not found!');
        }

        $imageData = $request->signature;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = time() . '.png';
        $directory = public_path('signatures');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $imageName;
        file_put_contents($filePath, base64_decode($imageData));

        $user = Authsign::find(session('authsign_id'));
        $user->signature = $imageName;
        $user->save();

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Signature created',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Signature Saved Successfully!');
    }

    public function logout()
    {
        ActivityLog::create([
            'authsign_id' => session('authsign_id'),
            'action'      => 'Logged out',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);

        session()->flush();
        return redirect()->route('login.form');
    }
}
