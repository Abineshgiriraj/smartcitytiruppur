<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class employeewisereportController extends Controller
{
    // Show list page
    public function index(Request $request)
    {
        $get_staff_name = DB::table('staff_registration')->get();
        return view('content.employeewisereport.list', compact('get_staff_name'));
    }

    // Fetch data for DataTables
    public function fetchData(Request $request)
    {
        DB::disableQueryLog();

        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        try {
            $pageQuery = DB::table('employee_report_view as v')
                ->select(
                    'v.unique_id as unique_id',
                    'v.name as name',
                    'v.designation_name as designation',
                    'v.zone_name as zone',
                    'v.ward_name as ward',
                    'v.department_name as department',
                    DB::raw('(SELECT COALESCE(SUM(e.total_complaints),0)
                              FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS total'),
                    DB::raw('(SELECT COALESCE(SUM(e.completed_complaints),0)
                              FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS completed'),
                    DB::raw('(SELECT COALESCE(SUM(e.pending_complaints),0)
                              FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS pending')
                )
                ->orderBy('v.name');

            if ($request->staff_name) {
                $pageQuery->where('v.unique_id', $request->staff_name);
            }

            if ($length > 0) {
                $pageQuery->offset($start)->limit($length);
            }

            $rows = $pageQuery->get();
            $pageCount = (int) $rows->count();

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $pageCount,
                'recordsFiltered' => $pageCount,
                'data'            => $rows,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

    // Export CSV
    public function export(Request $request)
    {
        DB::disableQueryLog();

        $query = DB::table('employee_report_view as v')
            ->select(
                'v.name as name',
                'v.designation_name as designation',
                'v.zone_name as zone',
                'v.ward_name as ward',
                'v.department_name as department',
                DB::raw('(SELECT COALESCE(SUM(e.total_complaints),0)
                          FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS total'),
                DB::raw('(SELECT COALESCE(SUM(e.completed_complaints),0)
                          FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS completed'),
                DB::raw('(SELECT COALESCE(SUM(e.pending_complaints),0)
                          FROM emp_comp_count e WHERE e.assign_staff = v.unique_id) AS pending')
            )
            ->orderBy('v.name');

        if ($request->staff_name) {
            $query->where('v.unique_id', $request->staff_name);
        }

        $data = $query->get();

        $filename = 'employeewise_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function () use ($data) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Name','Designation','Zone','Ward','Department','Total','Pending','Completed']);
            foreach ($data as $row) {
                fputcsv($fh, [
                    $row->name,
                    $row->designation,
                    $row->zone,
                    $row->ward,
                    $row->department,
                    $row->total,
                    $row->pending,
                    $row->completed
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
