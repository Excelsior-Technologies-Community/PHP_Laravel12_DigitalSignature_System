<?php

namespace App\Http\Controllers;

use App\Models\Authsign;
use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $totalUsers = Authsign::count();

        $totalDocs = Document::count();

        $signedDocs = Document::where('status', 'signed')->count();

        $pendingDocs = Document::where('status', 'pending')->count();

        $recentUsers = Authsign::oldest()
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::oldest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDocs',
            'signedDocs',
            'pendingDocs',
            'recentUsers',
            'recentLogs'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Users - Search + Sorting
    |--------------------------------------------------------------------------
    */

    public function users(Request $request)
    {
        $query = Authsign::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'created_at');

        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'name',
            'email',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->paginate(5)
            ->withQueryString();


        return view(
            'admin.users.index',
            compact('users')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | User Search
    |--------------------------------------------------------------------------
    */

    public function searchUsers(Request $request)
    {
        return redirect()->route(
            'admin.users',
            [
                'search' => $request->search,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Admin
    |--------------------------------------------------------------------------
    */

    public function toggleAdmin($id)
    {
        $user = Authsign::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Do not remove your own admin access
        |--------------------------------------------------------------------------
        */

        if ((int) $user->id === (int) session('authsign_id')) {

            return back()->with(
                'error',
                'You cannot change your own admin status.'
            );
        }

        $user->is_admin = !$user->is_admin;

        $user->save();


        ActivityLog::create([
            'authsign_id' => session('authsign_id'),
            'action'      => 'Toggled admin for ' . $user->name,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);


        return back()->with(
            'success',
            'User admin status updated successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */

    public function deleteUser($id)
    {
        $user = Authsign::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent deleting yourself
        |--------------------------------------------------------------------------
        */

        if ((int) $user->id === (int) session('authsign_id')) {

            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Signature Files
        |--------------------------------------------------------------------------
        */

        foreach ($user->signatures as $signature) {

            $signaturePath = public_path(
                'signatures/' . $signature->image_name
            );

            if (File::exists($signaturePath)) {
                File::delete($signaturePath);
            }

            $signature->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Document Files
        |--------------------------------------------------------------------------
        */

        foreach ($user->documents as $document) {

            $documentPath = public_path(
                'documents/' . $document->file_path
            );

            if (File::exists($documentPath)) {
                File::delete($documentPath);
            }

            $document->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Activity Logs
        |--------------------------------------------------------------------------
        */

        ActivityLog::where(
            'authsign_id',
            $user->id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | Delete User
        |--------------------------------------------------------------------------
        */

        $user->delete();


        /*
        |--------------------------------------------------------------------------
        | Admin Activity Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'authsign_id' => session('authsign_id'),
            'action'      => 'Deleted user: ' . $user->name,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);


        return back()->with(
            'success',
            'User deleted successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Documents - Search + Filter + Sorting
    |--------------------------------------------------------------------------
    */

    public function documents(Request $request)
    {
        $query = Document::with('user');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('file_name', 'like', '%' . $search . '%')
                    ->orWhere(
                        'verification_code',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $status = $request->status;

            if (in_array($status, ['pending', 'signed'])) {

                $query->where('status', $status);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'created_at');

        $direction = $request->get('direction', 'desc');


        $allowedSorts = [
            'file_name',
            'status',
            'created_at',
            'signed_at',
            'expires_at',
        ];


        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }


        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }


        $query->orderBy(
            $sort,
            $direction
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $documents = $query
            ->paginate(5)
            ->withQueryString();


        return view(
            'admin.documents.index',
            compact('documents')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Activity Logs - Search + Filter
    |--------------------------------------------------------------------------
    */

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'action',
                    'like',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'ip_address',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Action Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {

            $query->where(
                'action',
                'like',
                '%' . $request->action . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Oldest Logs First
        |--------------------------------------------------------------------------
        */

        $logs = $query
            ->oldest('created_at')
            ->paginate(5)
            ->withQueryString();


        return view(
            'admin.activity-logs',
            compact('logs')
        );
    }
}
