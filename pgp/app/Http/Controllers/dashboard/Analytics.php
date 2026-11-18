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

        $sources = [
            'pgp' => ['label' => 'Previous PGP records', 'total' => null, 'pending' => null, 'completed' => null, 'other' => null],
            'smartcity' => ['label' => 'Smart City Tiruppur records', 'total' => null, 'pending' => null, 'completed' => null, 'other' => null],
        ];
        $warnings = [];

        // Source 1: original PGP complaint schema. Status codes are PGP-specific.
        try {
            if (Schema::connection('mysql')->hasTable('new_complaints')) {
                $base = DB::connection('mysql')->table('new_complaints')->where('is_delete', 0);
                $sources['pgp']['total'] = (clone $base)->count();
                $sources['pgp']['pending'] = (clone $base)->where('status', 0)->count();
                $sources['pgp']['completed'] = (clone $base)->where('status', 2)->count();
                $sources['pgp']['other'] = max(0, $sources['pgp']['total'] - $sources['pgp']['pending'] - $sources['pgp']['completed']);
            } else {
                $warnings[] = 'PGP complaint records are unavailable.';
            }
        } catch (\Throwable $e) {
            \Log::warning('PGP dashboard source unavailable', ['error' => $e->getMessage()]);
            $warnings[] = 'PGP complaint summary could not be read.';
        }

        // Source 2: standalone PHP portal's native schema. Separate, read-only connection.
        try {
            if (Schema::connection('smartcity')->hasTable('complaints')) {
                $base = DB::connection('smartcity')->table('complaints')->where('is_deleted', 0);
                $sources['smartcity']['total'] = (clone $base)->count();
                $sources['smartcity']['pending'] = (clone $base)->whereIn('status', [
                    'Registered', 'Assigned', 'Acknowledged', 'In Progress', 'Reopened'
                ])->count();
                $sources['smartcity']['completed'] = (clone $base)->whereIn('status', ['Resolved', 'Closed'])->count();
                $sources['smartcity']['other'] = max(0, $sources['smartcity']['total'] - $sources['smartcity']['pending'] - $sources['smartcity']['completed']);
            } else {
                $warnings[] = 'Smart City Tiruppur complaints table is unavailable.';
            }
        } catch (\Throwable $e) {
            \Log::warning('Smart City dashboard source unavailable', ['error' => $e->getMessage()]);
            $warnings[] = 'Smart City Tiruppur complaint summary could not be read. Check the SMARTCITY_DB_* settings.';
        }

        return view('content.dashboard.dashboards-analytics', compact('sources', 'warnings'));
    }
}
