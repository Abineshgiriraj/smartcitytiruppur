<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class escalationreportController extends Controller
{
    // DB VIEW name
    protected $viewName = 'escalation_report';

    /**
     * List page (DataTables AJAX)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table($this->viewName)
                ->selectRaw('
                    COALESCE(staff_name, \'\') AS staff_name,
                    COALESCE(mobile_no, \'\') AS mobile_no,
                    SUM(COALESCE(pending_count,0))   AS pending_count,
                    SUM(COALESCE(completed_count,0)) AS completed_count
                ')
                ->groupBy('staff_name', 'mobile_no') // include mobile_no in groupBy for MySQL strict mode
                ->orderBy('staff_name', 'asc');

            return DataTables::of($data)
                ->addIndexColumn() // adds DT_RowIndex
                ->make(true);
        }

        return view('content.escalation_report.list'); // Blade view
    }

    /**
     * Export CSV (Excel button)
     */
    public function export(Request $request)
    {
        $data = DB::table($this->viewName)
            ->selectRaw('
                COALESCE(staff_name, \'\') AS staff_name,
                COALESCE(mobile_no, \'\')  AS mobile_no,
                SUM(COALESCE(pending_count,0))   AS pending_count,
                SUM(COALESCE(completed_count,0)) AS completed_count
            ')
            ->groupBy('staff_name', 'mobile_no')
            ->orderBy('staff_name', 'asc')
            ->get();

        $filename = 'escalation_report_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $fh = fopen('php://output', 'w');
            // optional BOM for Excel compatibility
            fprintf($fh, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($fh, ['S.No', 'Staff Name', 'Mobile No', 'Pending Count', 'Completed Count']);
            $i = 1;
            foreach ($data as $r) {
                fputcsv($fh, [
                    $i++,
                    $r->staff_name !== '' ? $r->staff_name : '-',
                    $r->mobile_no  !== '' ? $r->mobile_no  : '-',
                    $r->pending_count ?? 0,
                    $r->completed_count ?? 0,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print page
     */
    public function print(Request $request)
    {
        $rows = DB::table($this->viewName)
            ->selectRaw('
                COALESCE(staff_name, \'\') AS staff_name,
                COALESCE(mobile_no, \'\')  AS mobile_no,
                SUM(COALESCE(pending_count,0))   AS pending_count,
                SUM(COALESCE(completed_count,0)) AS completed_count
            ')
            ->groupBy('staff_name', 'mobile_no')
            ->orderBy('staff_name', 'asc')
            ->get();

        return view('content.escalation_report.print', compact('rows'));
    }
}
