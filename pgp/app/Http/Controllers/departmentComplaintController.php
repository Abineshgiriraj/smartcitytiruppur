<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class departmentComplaintController extends Controller
{
    // List page
    public function index(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $dept = $request->get('department');

        return view('content.departmentcomplaint.list', compact('from', 'to', 'dept'));
    }

    // Details modal data
    public function details(Request $request)
    {
        $deptId = $request->dept_id;
        $catId  = $request->category_id;

        $records = DB::table('new_complaints as nc')
            ->leftJoin('department as d', 'd.unique_id', '=', 'nc.department')
            ->leftJoin('department_category as c', 'c.unique_id', '=', 'nc.department_category')
            ->select('nc.grievance_id', 'nc.name', 'nc.mobile_no', 'nc.address1', 'd.department', 'c.department_category', 'nc.status')
            ->where('nc.department', $deptId)
            ->where('nc.department_category', $catId)
            ->get();

        return view('content.departmentcomplaint.details', compact('records'));
    }

    // Excel export
    public function export(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $dept = $request->get('department');

        $query = DB::table('new_complaints as nc')
            ->leftJoin('department as d', 'd.unique_id', '=', 'nc.department')
            ->leftJoin('department_category as c', 'c.unique_id', '=', 'nc.department_category')
            ->select('d.department', 'c.department_category', 'nc.status', 'nc.created_at');

        if ($from) $query->whereDate('nc.created_at', '>=', $from);
        if ($to) $query->whereDate('nc.created_at', '<=', $to);
        if ($dept) $query->where('nc.department', $dept);

        $rows = $query->get();

        $filename = "DepartmentComplaintReport_" . now()->format('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo "Department\tCategory\tStatus\tDate\n";
        foreach ($rows as $r) {
            echo "{$r->department}\t{$r->department_category}\t{$r->status}\t{$r->created_at}\n";
        }
        exit;
    }

    // Print page (new)
    public function print(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $dept = $request->get('department');

        $departments = DB::table('department')->get();
        $categories  = DB::table('department_category')->get();

        $headerPath = public_path('resources/content/departmentcomplaint/header.png');
        $headerDataUrl = null;
        if (file_exists($headerPath)) {
            $mime = mime_content_type($headerPath);
            $headerDataUrl = "data:$mime;base64," . base64_encode(file_get_contents($headerPath));
        }

        return view('content.departmentcomplaint.print', compact('departments', 'categories', 'from', 'to', 'dept', 'headerDataUrl'));
    }
}
