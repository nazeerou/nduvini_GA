<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /* ============================================================
     |  ACCESS GUARD
     |  Restrict to admins (adjust to your role logic)
     | ============================================================ */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            // Example: only admins can view audit logs.
            // Replace `isAdmin()` with your own role check, e.g.:
            //   $user->role_id == Role::where('name','Admin')->first()->id
            //   $user->hasRole('Admin')
            if (!$user || !$this->isAdmin($user)) {
                abort(403, 'You are not allowed to view audit logs.');
            }

            return $next($request);
        });
    }

    protected function isAdmin($user): bool
    {
        // TODO: adjust to your project's role logic.
        // Returning true for now so it works out of the box.
        // Change to real check, e.g.:
        //   return $user->role_id == 1;
        //   return $user->hasRole('Admin');
        return true;
    }

    /* ============================================================
     |  INDEX — list with filters
     | ============================================================ */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter by reference
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Only critical
        if ($request->boolean('critical')) {
            $query->where('is_critical', true);
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search across subject / description
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('subject', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        // Data for filter dropdowns
        $users       = User::orderBy('fname')->get(['id', 'fname', 'lname']);
        $actions     = AuditLog::select('action')->distinct()->pluck('action');
        $severities  = AuditLog::select('severity')->distinct()->pluck('severity');
        $modelTypes  = AuditLog::select('model_type')->whereNotNull('model_type')->distinct()->pluck('model_type');

        // Quick stats
        $stats = [
            'total'     => AuditLog::count(),
            'critical'  => AuditLog::where('is_critical', true)->count(),
            'today'     => AuditLog::whereDate('created_at', today())->count(),
            'deletes'   => AuditLog::where('action', 'delete')->count(),
        ];

        return view('audit-logs.index', compact(
            'logs', 'users', 'actions', 'severities', 'modelTypes', 'stats'
        ));
    }

    /* ============================================================
     |  CRITICAL — quick view of critical-only logs
     | ============================================================ */
    public function critical(Request $request)
    {
        $request->merge(['critical' => true]);
        return $this->index($request);
    }

    /* ============================================================
     |  SHOW — full detail of one log entry
     | ============================================================ */
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('audit-logs.show', compact('log'));
    }

    /* ============================================================
     |  TIMELINE — everything that happened to one reference
     | ============================================================ */
    public function timeline($reference)
    {
        $logs = AuditLog::with('user')
            ->where('reference', $reference)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('audit-logs.timeline', compact('logs', 'reference'));
    }

    /* ============================================================
     |  PURGE — admin only, delete old logs
     | ============================================================ */
    public function purge(Request $request)
    {
        $request->validate([
            'older_than_days' => 'required|integer|min:30',
        ]);

        $cutoff = now()->subDays((int) $request->older_than_days);

        $deleted = DB::table('audit_logs')
            ->where('created_at', '<', $cutoff)
            ->where('is_critical', false)     // NEVER purge critical logs
            ->delete();

        // Log the purge itself (self-auditing)
        \App\Services\AuditLogger::log(
            'Audit logs purged',
            'delete',
            'audit_logs',
            null,
            [],
            [
                'older_than_days' => (int) $request->older_than_days,
                'rows_deleted'    => $deleted,
                'cutoff'          => $cutoff->toDateTimeString(),
            ],
            "Purged {$deleted} audit log(s) older than {$request->older_than_days} days (critical logs preserved).",
            false
        );

        return redirect()->route('audit-logs.index')
            ->with('message', "Purged {$deleted} old log(s). Critical logs were preserved.");
    }
}