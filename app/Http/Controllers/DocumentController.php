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
    /**
     * Show user's documents.
     */
    public function index()
    {
        $user = Authsign::findOrFail(session('authsign_id'));

        $documents = $user->documents()
            ->latest()
            ->get();

        return view('authsign.documents.index', compact(
            'user',
            'documents'
        ));
    }

    /**
     * Upload document.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ], [
            'document.required' => 'Please select a document.',
            'document.max' => 'Document size must not exceed 10 MB.',
            'expires_at.date' => 'Please enter a valid expiry date.',
            'expires_at.after_or_equal' => 'Expiry date cannot be before today.',
        ]);

        $file = $request->file('document');

        $fileName = time()
            . '_'
            . Str::random(10)
            . '.'
            . $file->getClientOriginalExtension();

        $directory = public_path('documents');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        $file->move($directory, $fileName);

        $user = Authsign::findOrFail(session('authsign_id'));

        /*
         * Generate unique verification code.
         * Example:
         * DS-A8K92P4X7M
         */
        do {
            $verificationCode = 'DS-' . strtoupper(Str::random(10));
        } while (
            Document::where('verification_code', $verificationCode)->exists()
        );

        $document = Document::create([
            'authsign_id' => $user->id,
            'file_name' => $originalName,
            'file_path' => $fileName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => 'pending',
            'expires_at' => $request->expires_at,
            'verification_code' => $verificationCode,
        ]);

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action' => 'Document uploaded: ' . $document->file_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('documents.index')
            ->with(
                'success',
                'Document uploaded successfully! Verification ID: '
                . $verificationCode
            );
    }

    /**
     * Show sign page.
     */
    public function signForm($id)
    {
        $user = Authsign::findOrFail(session('authsign_id'));

        $document = Document::where('id', $id)
            ->where('authsign_id', $user->id)
            ->firstOrFail();

        /*
         * Do not allow signing expired documents.
         */
        if ($document->isExpired()) {
            return redirect()
                ->route('documents.index')
                ->with(
                    'error',
                    'This document has expired and cannot be signed.'
                );
        }

        return view(
            'authsign.documents.sign',
            compact('user', 'document')
        );
    }

    /**
     * Sign document.
     */
    public function sign(Request $request, $id)
    {
        $user = Authsign::findOrFail(session('authsign_id'));

        $document = Document::where('id', $id)
            ->where('authsign_id', $user->id)
            ->firstOrFail();

        if (!$user->signature) {
            return redirect()
                ->route('signature.form')
                ->with(
                    'error',
                    'Please create a signature first.'
                );
        }

        /*
         * Prevent signing expired documents.
         */
        if ($document->isExpired()) {
            return redirect()
                ->route('documents.index')
                ->with(
                    'error',
                    'This document has expired and cannot be signed.'
                );
        }

        /*
         * Prevent signing an already signed document.
         */
        if ($document->status === 'signed') {
            return redirect()
                ->route('documents.index')
                ->with(
                    'error',
                    'This document is already signed.'
                );
        }

        $document->status = 'signed';
        $document->signed_at = now();

        /*
         * Make sure older documents without a verification code
         * also receive one when signed.
         */
        if (!$document->verification_code) {
            do {
                $verificationCode = 'DS-' . strtoupper(Str::random(10));
            } while (
                Document::where(
                    'verification_code',
                    $verificationCode
                )->exists()
            );

            $document->verification_code = $verificationCode;
        }

        $document->save();

        ActivityLog::create([
            'authsign_id' => $user->id,
            'action' => 'Document signed: ' . $document->file_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('documents.index')
            ->with(
                'success',
                'Document signed successfully!'
            );
    }

    /**
     * Verify document using document ID.
     */
    public function verify($id)
    {
        $document = Document::with('user')
            ->findOrFail($id);

        $signed = $document->status === 'signed';
        $expired = $document->isExpired();
        $valid = $document->isValid();

        return view(
            'authsign.documents.verify',
            compact(
                'document',
                'signed',
                'expired',
                'valid'
            )
        );
    }

    /**
     * Public verification form.
     */
    public function verificationForm()
    {
        return view('authsign.documents.verify-code');
    }

    /**
     * Verify document using verification ID.
     */
    public function verifyByCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|max:30',
        ], [
            'verification_code.required' =>
                'Please enter the verification ID.',
        ]);

        $code = strtoupper(trim($request->verification_code));

        $document = Document::with('user')
            ->where('verification_code', $code)
            ->first();

        if (!$document) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid verification ID. Document not found.'
                );
        }

        $signed = $document->status === 'signed';
        $expired = $document->isExpired();
        $valid = $document->isValid();

        return view(
            'authsign.documents.verify',
            compact(
                'document',
                'signed',
                'expired',
                'valid'
            )
        );
    }

    /**
     * Download document.
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        $user = Authsign::find(session('authsign_id'));

        if (!$user || $document->authsign_id !== $user->id) {
            return back()
                ->with('error', 'Unauthorized access.');
        }

        $filePath = public_path(
            'documents/' . $document->file_path
        );

        if (!File::exists($filePath)) {
            return back()
                ->with('error', 'File not found.');
        }

        return response()->download(
            $filePath,
            $document->file_name
        );
    }
}