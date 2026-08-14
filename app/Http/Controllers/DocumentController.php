<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Authsign;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Authsign::find(session('authsign_id'));
        $documents = $user->documents()->latest()->get();
        return view('authsign.documents.index', compact('user', 'documents'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
        ]);

        $file = $request->file('document');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $directory = public_path('documents');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        $file->move($directory, $fileName);

        $user = Authsign::find(session('authsign_id'));

        $document = Document::create([
            'authsign_id' => $user->id,
            'file_name'   => $originalName,
            'file_path'   => $fileName,
            'mime_type'   => $mimeType,
            'file_size'   => $fileSize,
            'status'      => 'pending',
        ]);

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Document uploaded',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully!');
    }

    public function signForm($id)
    {
        $user = Authsign::find(session('authsign_id'));
        $document = Document::where('id', $id)->where('authsign_id', $user->id)->firstOrFail();
        return view('authsign.documents.sign', compact('user', 'document'));
    }

    public function sign(Request $request, $id)
    {
        $user = Authsign::find(session('authsign_id'));
        $document = Document::where('id', $id)->where('authsign_id', $user->id)->firstOrFail();

        if (!$user->signature) {
            return redirect()->route('signature.form')->with('error', 'Please create a signature first');
        }

        $document->status = 'signed';
        $document->signed_at = now();
        $document->save();

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action'      => 'Document signed: ' . $document->file_name,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document signed successfully!');
    }

    public function verify($id)
    {
        $document = Document::with('user')->findOrFail($id);
        $signed = $document->status === 'signed';
        return view('authsign.documents.verify', compact('document', 'signed'));
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Authsign::find(session('authsign_id'));

        if (!$user || $document->authsign_id !== $user->id) {
            return back()->with('error', 'Unauthorized');
        }

        $filePath = public_path('documents/' . $document->file_path);
        if (!File::exists($filePath)) {
            return back()->with('error', 'File not found');
        }

        return response()->download($filePath, $document->file_name);
    }
}
