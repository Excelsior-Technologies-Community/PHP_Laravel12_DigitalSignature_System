<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authsign;
use Illuminate\Support\Facades\Hash;

class AuthsignController extends Controller
{
    // -------------------
    // REGISTER PAGE
    // -------------------
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

    Authsign::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('login.form')
        ->with('success', 'Registration successful! Please login.');
}

    // -------------------
    // LOGIN PAGE
    // -------------------
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

    session([
        'authsign_id'   => $user->id,
        'authsign_name' => $user->name,
    ]);

    // Signature compulsory
    if (!$user->signature) {
        return redirect()->route('signature.form');
    }

    return redirect()->route('dashboard')
        ->with('success', 'Login successful!');
}


    // -------------------
    // DASHBOARD
    // -------------------
    public function dashboard()
    {
        $user = Authsign::find(session('authsign_id'));

       
        if (!$user->signature) {
            return redirect()->route('signature.form');
        }

        return view('authsign.dashboard', compact('user'));
    }

    // -------------------
    // SIGNATURE PAGE
    // -------------------
   public function signatureForm()
{
    $user = Authsign::find(session('authsign_id'));
    return view('authsign.signature', compact('user'));
}


    // -------------------
    // SAVE SIGNATURE
    // -------------------
    public function signatureSave(Request $request)
    {
        if (!$request->signature) {
            return back()->with('error', 'Signature not found!');
        }

        $imageData = $request->signature;

        // Remove base64 header
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // File name
        $imageName = time() . '.png';

        // Directory path
        $directory = public_path('signatures');

        // 🔥 Auto-create folder if missing
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Full file path
        $filePath = $directory . DIRECTORY_SEPARATOR . $imageName;

        // Save image
        file_put_contents($filePath, base64_decode($imageData));

        // Save to DB
        $user = Authsign::find(session('authsign_id'));
        $user->signature = $imageName;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Signature Saved Successfully!');
    }

    // -------------------
    // LOGOUT
    // -------------------
    public function logout()
    {
        session()->flush();
        return redirect()->route('login.form');
    }
}
