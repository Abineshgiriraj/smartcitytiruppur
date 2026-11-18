<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Analytics extends Controller
{
    public function index()
    {
        if (!session('user_id')) {
            return redirect('/')->withErrors(['error' => 'Please sign in to access the dashboard.']);
        }

        $stats = ['total' => null, 'pending' => null, 'completed' => null, 'other' => null];
        $dataError = null;
        try {
            if (Schema::hasTable('new_complaints')) {
                $rows = DB::table('new_complaints')->where('is_delete', 0);
                $stats['total'] = (clone $rows)->count();
                $stats['pending'] = (clone $rows)->where('status', 0)->count();
                $stats['completed'] = (clone $rows)->where('status', 2)->count();
                $stats['other'] = max(0, $stats['total'] - $stats['pending'] - $stats['completed']);
            } else {
                $dataError = 'The PGP complaints table is not configured in this database.';
            }
        } catch (\Throwable $e) {
            \Log::warning('Municipal dashboard summary unavailable', ['error' => $e->getMessage()]);
            $dataError = 'Complaint summary is temporarily unavailable.';
        }

        return view('content.dashboard.dashboards-analytics', compact('stats', 'dataError'));
    }
}
