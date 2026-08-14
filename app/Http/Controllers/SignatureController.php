<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authsign;
use App\Models\Signature;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;

class SignatureController extends Controller
{
    public function index()
    {
        $user = Authsign::find(session('authsign_id'));
        return view('authsign.signature', compact('user'));
    }

    public function store(Request $request)
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
        $signature = Signature::create([
            'authsign_id' => $user->id,
            'image_name'  => $imageName,
        ]);

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Signature saved',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return redirect()->route('signatures.all')
            ->with('success', 'Signature saved!');
    }

    public function all()
    {
        $user = Authsign::find(session('authsign_id'));
        $signatures = $user->signatures()->latest()->get();
        return view('authsign.signatures.index', compact('user', 'signatures'));
    }

    public function download($id)
    {
        $signature = Signature::findOrFail($id);
        $user = Authsign::find(session('authsign_id'));

        if ($signature->authsign_id !== $user->id) {
            return back()->with('error', 'Unauthorized');
        }

        $filePath = public_path('signatures/' . $signature->image_name);
        if (!File::exists($filePath)) {
            return back()->with('error', 'File not found');
        }

        return response()->download($filePath, $signature->image_name);
    }

    public function destroy($id)
    {
        $signature = Signature::findOrFail($id);
        $user = Authsign::find(session('authsign_id'));

        if ($signature->authsign_id !== $user->id) {
            return back()->with('error', 'Unauthorized');
        }

        $filePath = public_path('signatures/' . $signature->image_name);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $signature->delete();

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Signature deleted',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'Signature deleted successfully');
    }
}
