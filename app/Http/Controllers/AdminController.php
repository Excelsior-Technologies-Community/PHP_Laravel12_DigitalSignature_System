<?php

namespace App\Http\Controllers;

use App\Models\Authsign;
use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = Authsign::count();
        $totalDocs = Document::count();
        $signedDocs = Document::where('status', 'signed')->count();
        $pendingDocs = Document::where('status', 'pending')->count();
        $recentUsers = Authsign::latest()->take(5)->get();
        $recentLogs = ActivityLog::latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDocs', 'signedDocs', 'pendingDocs',
            'recentUsers', 'recentLogs'
        ));
    }

    public function users(Request $request)
    {
        $query = Authsign::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function searchUsers(Request $request)
    {
        return redirect()->route('admin.users', ['search' => $request->search]);
    }

    public function toggleAdmin($id)
    {
        $user = Authsign::findOrFail($id);
        $user->is_admin = !$user->is_admin;
        $user->save();

        ActivityLog::create([
            'authsign_id' => session('authsign_id'),
            'action'      => 'Toggled admin for ' . $user->name,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'User admin status updated');
    }

    public function documents()
    {
        $documents = Document::with('user')->latest()->paginate(10);
        return view('admin.documents.index', compact('documents'));
    }

    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('admin.activity-logs', compact('logs'));
    }
}
