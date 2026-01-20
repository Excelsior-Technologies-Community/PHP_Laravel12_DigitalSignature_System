# PHP_Laravel12_DigitalSignature_System

# Step 1 : Install Laravel 12 
```php
composer create-project laravel/laravel PHP_Laravel12_DigitalSignature_System
```
# Step 2 : Setup database for .env file
```php
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=Your database name 
 DB_USERNAME=root
 DB_PASSWORD=
```
# Create Project For Simple Digital signature store
# Step 3 : Create migration file for database create 
```php
php artisan make:model Authsign –m
```
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('authsigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('signature')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authsigns');
    }
};
```
# Now Run Migration
```php
php artisan migrate
```
# Step 4 : Create Authsign Model 
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authsign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'signature'
    ];

    protected $hidden = [
        'password'
    ];
}
```
# Step 5 : Create Controller
```php
php artisan make:controller AuthsignController
```
```php
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
        return view('authsign.signature');
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
```
# Step 6  : Create Route for web.php file
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthsignController;

// Register
Route::get('register', [AuthsignController::class, 'registerForm'])->name('register.form');
Route::post('register', [AuthsignController::class, 'register'])->name('register.save');

// Login
Route::get('login', [AuthsignController::class, 'loginForm'])->name('login.form');
Route::post('login', [AuthsignController::class, 'login'])->name('login.check');

// Dashboard
Route::get('dashboard', [AuthsignController::class, 'dashboard'])
    ->middleware('authsign')
    ->name('dashboard');

// Signature Page
Route::get('signature', [AuthsignController::class, 'signatureForm'])
    ->middleware('authsign')
    ->name('signature.form');

// Signature Save
Route::post('signature/save', [AuthsignController::class, 'signatureSave'])
    ->middleware('authsign')
    ->name('signature.save');

// Logout
Route::get('logout', [AuthsignController::class, 'logout'])->name('logout');

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});
```
# Step 7 : Create Authsign Middleware
```php
php artisan make:middleware AuthsignMiddleware
```
```php
<?php

namespace App\Http\Middleware;

use Closure;

class AuthsignMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('authsign_id')) {
            return redirect()->route('login.form')->with('error', 'Please login first');
        }

        return $next($request);
    }
}
```
# Setup bootstrap/app.php file 
```php
 ->withMiddleware(function (Middleware $middleware): void {

       
        $middleware->alias([
            'authsign' => \App\Http\Middleware\AuthsignMiddleware::class,
        ]);

    })
```
# Step 8 : Create register,login,dashboard and signature lade file for resource/view/authsign folder
# resource/view/authsign/register.blade.php
```php
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-4">
    <div class="card p-4 shadow">

        <h3>Register</h3>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form method="POST" action="{{ route('register.save') }}">
            @csrf

            <input name="name" class="form-control mt-3" placeholder="Name" required>
            <input name="email" class="form-control mt-3" placeholder="Email" required>
           <input type="password" name="password" class="form-control mt-3" placeholder="Password" required>

<input type="password" name="password_confirmation"
       class="form-control mt-3"
       placeholder="Confirm Password"
       required>

            <button class="btn btn-primary mt-3 w-100">Register</button>
        </form>

        <p class="mt-3">Already have account?
            <a href="{{ route('login.form') }}">Login</a>
        </p>

    </div>
</div>

</body>
</html>
```
# resource/view/authsign/login.blade.php
```php
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-4">
    <div class="card p-4 shadow">

        <h3>Login</h3>

       {{-- Success message --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Error message --}}
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- Validation errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form method="POST" action="{{ route('login.check') }}">
            @csrf

            <input name="email" class="form-control mt-3" placeholder="Email" required>
            <input type="password" name="password" class="form-control mt-3" placeholder="Password" required>

            <button class="btn btn-success mt-3 w-100">Login</button>
        </form>

        <p class="mt-3">No account?
            <a href="{{ route('register.form') }}">Register</a>
        </p>

    </div>
</div>

</body>
</html>
```
# resource/view/authsign/dashboard.blade.php
```php
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2>Welcome, {{ session('authsign_name') }}</h2>

    <a href="{{ route('signature.form') }}" class="btn btn-primary mt-3">Add / Update Signature</a>
    <a href="{{ route('logout') }}" class="btn btn-danger mt-3">Logout</a>

    @if($user->signature)
        <h4 class="mt-4">Your Signature:</h4>
        <img src="/signatures/{{ $user->signature }}" width="250">
    @endif

</div>

</body>
</html>
```

# resource/view/authsign/signature.blade.php
```php
<!DOCTYPE html>
<html>
<head>
<title>Digital Signature</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
    #signature-pad {
        border: 2px solid #000;
        width: 100%;
        height: 250px;
        touch-action: none; /* IMPORTANT for mobile */
        background: #fff;
    }
</style>
</head>
<body>

<div class="container mt-4 col-md-6">

    <h3>Draw Your Digital Signature</h3>

    <canvas id="signature-pad"></canvas>

    <form action="{{ route('signature.save') }}" method="POST">
        @csrf
        <input type="hidden" id="signature" name="signature">

        <button type="submit" class="btn btn-success mt-3" onclick="saveSignature()">Save Signature</button>
        <button type="button" class="btn btn-danger mt-3 ms-2" onclick="clearPad()">Clear</button>
    </form>

</div>

<script>
const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');

let drawing = false;
let lastX = 0;
let lastY = 0;

// Fix canvas resolution
function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    ctx.scale(ratio, ratio);

    ctx.lineWidth = 3;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
}

resizeCanvas();
window.addEventListener('resize', resizeCanvas);

// Get mouse / touch position
function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    if (e.touches) {
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    }
    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top
    };
}

// Start drawing
function startDraw(e) {
    drawing = true;
    const pos = getPos(e);
    lastX = pos.x;
    lastY = pos.y;
}

// Draw
function draw(e) {
    if (!drawing) return;
    e.preventDefault();

    const pos = getPos(e);

    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();

    lastX = pos.x;
    lastY = pos.y;
}

// Stop drawing
function stopDraw() {
    drawing = false;
}

// Mouse events
canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDraw);
canvas.addEventListener('mouseleave', stopDraw);

// Touch events
canvas.addEventListener('touchstart', startDraw);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', stopDraw);

// Clear pad
function clearPad() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Save signature
function saveSignature() {
    document.getElementById('signature').value = canvas.toDataURL('image/png');
}
</script>

</body>
</html>
```
# Now Run Server and paste this url from browser
```php
php artisan serve
```
```php
http://127.0.0.1:8000/register
```
 <img width="1634" height="620" alt="image" src="https://github.com/user-attachments/assets/200025c4-67fd-4c5d-9a0b-aa80fdb94fc2" />
<img width="1647" height="579" alt="image" src="https://github.com/user-attachments/assets/4a4aa6e2-290c-4d61-842a-9c21790b5b65" />
<img width="1598" height="403" alt="image" src="https://github.com/user-attachments/assets/6aff482a-d323-4792-b447-714f91bc2d88" />
<img width="1547" height="383" alt="image" src="https://github.com/user-attachments/assets/68dfb510-36f5-4e5c-ad10-a2731a469ec2" />

 
 
 
 










