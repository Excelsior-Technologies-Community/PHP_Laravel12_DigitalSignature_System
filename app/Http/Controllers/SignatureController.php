<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authsign;
use App\Models\Signature;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;

class SignatureController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Signature Drawing Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login first.');
        }

        return view('authsign.signature', compact('user'));
    }


    /*
    |--------------------------------------------------------------------------
    | Save New Signature
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login first.');
        }

        if (!$request->signature) {
            return back()->with('error', 'Please draw your signature first.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Base64 Signature
        |--------------------------------------------------------------------------
        */

        $imageData = $request->signature;

        if (!str_starts_with($imageData, 'data:image/png;base64,')) {
            return back()->with('error', 'Invalid signature format.');
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Base64 Prefix
        |--------------------------------------------------------------------------
        */

        $imageData = str_replace(
            'data:image/png;base64,',
            '',
            $imageData
        );

        $imageData = str_replace(' ', '+', $imageData);

        $decodedImage = base64_decode($imageData, true);

        if ($decodedImage === false) {
            return back()->with('error', 'Invalid signature data.');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Signature Directory
        |--------------------------------------------------------------------------
        */

        $directory = public_path('signatures');

        if (!File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0777,
                true,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Unique Filename
        |--------------------------------------------------------------------------
        */

        $imageName = uniqid('signature_', true) . '.png';

        $filePath = $directory . DIRECTORY_SEPARATOR . $imageName;

        /*
        |--------------------------------------------------------------------------
        | Save Image
        |--------------------------------------------------------------------------
        */

        file_put_contents(
            $filePath,
            $decodedImage
        );

        /*
        |--------------------------------------------------------------------------
        | Save Signature History
        |--------------------------------------------------------------------------
        */

        Signature::create([
            'authsign_id' => $user->id,
            'image_name' => $imageName,
        ]);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        | Update Current Signature
        |--------------------------------------------------------------------------
        */

        /*
         * Delete the previous current signature file
         * only if it is different from the new one.
         */

        if (
            $user->signature &&
            $user->signature !== $imageName
        ) {
            $oldSignaturePath = public_path(
                'signatures/' . $user->signature
            );

            if (File::exists($oldSignaturePath)) {
                File::delete($oldSignaturePath);
            }
        }

        /*
         * Store new signature filename
         * in authsigns.signature
         */

        $user->signature = $imageName;
        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action' => 'Signature saved',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('signatures.all')
            ->with('success', 'Signature saved successfully!');
    }


    /*
    |--------------------------------------------------------------------------
    | Show All Signatures
    |--------------------------------------------------------------------------
    */

    public function all()
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login first.');
        }

        $signatures = $user
            ->signatures()
            ->latest()
            ->get();

        return view(
            'authsign.signatures.index',
            compact('user', 'signatures')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Download Signature
    |--------------------------------------------------------------------------
    */

    public function download($id)
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login first.');
        }

        $signature = Signature::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        */

        if ((int) $signature->authsign_id !== (int) $user->id) {
            return back()->with('error', 'Unauthorized.');
        }

        /*
        |--------------------------------------------------------------------------
        | File Path
        |--------------------------------------------------------------------------
        */

        $filePath = public_path(
            'signatures/' . $signature->image_name
        );

        if (!File::exists($filePath)) {
            return back()->with('error', 'Signature file not found.');
        }

        return response()->download(
            $filePath,
            $signature->image_name
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Signature
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $user = Authsign::find(session('authsign_id'));

        if (!$user) {
            return redirect()->route('login.form')
                ->with('error', 'Please login first.');
        }

        $signature = Signature::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        */

        if ((int) $signature->authsign_id !== (int) $user->id) {
            return back()->with('error', 'Unauthorized.');
        }

        /*
        |--------------------------------------------------------------------------
        | Delete File
        |--------------------------------------------------------------------------
        */

        $filePath = public_path(
            'signatures/' . $signature->image_name
        );

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        /*
        |--------------------------------------------------------------------------
        | Check if this is the current signature
        |--------------------------------------------------------------------------
        */

        if ($user->signature === $signature->image_name) {

            /*
             * Find another signature to use as current signature.
             */

            $latestSignature = Signature::where(
                'authsign_id',
                $user->id
            )
                ->where('id', '!=', $signature->id)
                ->latest()
                ->first();

            if ($latestSignature) {
                $user->signature = $latestSignature->image_name;
            } else {
                $user->signature = null;
            }

            $user->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Database Record
        |--------------------------------------------------------------------------
        */

        $signature->delete();

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action' => 'Signature deleted',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with(
            'success',
            'Signature deleted successfully.'
        );
    }
}

