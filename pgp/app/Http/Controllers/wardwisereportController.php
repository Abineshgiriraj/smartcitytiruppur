<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class wardwisereportController extends Controller
{
    public function index(Request $request)
    {
        return view()->first([
            'content.wardwisereport.list',
            'content.wardwisereport.index',
            'content.wardwisereport',
        ]);
    }

    public function details(Request $request)
    {
        return view()->first([
            'content.wardwisereport.details',
            'content.wardwisereport_detail',
        ]);
    }

    // ✅ Excel export (CSV) for Ward-wise (Ward, Department, Pending, Resolved, Total)
    public function export(Request $request)
    {
        $dateCol    = $this->pickCol('new_complaints',['created_at','date','occur_time','complaint_date']);
        $deptCol    = $this->pickCol('new_complaints',['department','dept','dept_id','department_id']);
        $wardCol    = $this->pickCol('new_complaints',['ward','ward_id','wardid','ward_unique_id']);
        $statusCol  = $this->pickCol('new_complaints',['status','complaint_status']);
        $hasIsDelete = \Schema::hasColumn('new_complaints','is_delete');

        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $ward = $request->get('ward');

        // All wards (or selected ward only)
        $wardQuery = DB::table('ward')
            ->when(\Schema::hasColumn('ward','is_delete'), fn($q)=>$q->where('is_delete',0))
            ->when(\Schema::hasColumn('ward','is_active'), fn($q)=>$q->where('is_active',1))
            ->select(['unique_id','ward'])
            ->orderBy('ward');

        $wards = $ward ? $wardQuery->where('unique_id',$ward)->get()
                       : $wardQuery->get();

        // All departments
        $departments = DB::table('department')
            ->when(\Schema::hasColumn('department','is_delete'), fn($q)=>$q->where('is_delete',0))
            ->when(\Schema::hasColumn('department','is_active'), fn($q)=>$q->where('is_active',1))
            ->select(['unique_id','department'])
            ->orderBy('department')
            ->get();

        $out = [];
        foreach ($wards as $w) {
            foreach ($departments as $d) {
                $base = DB::table('new_complaints')
                    ->when($hasIsDelete, fn($q)=>$q->where('is_delete',0))
                    ->when($wardCol, fn($q)=>$q->where($wardCol, $w->unique_id))
                    ->when($deptCol, fn($q)=>$q->where($deptCol, $d->unique_id))
                    ->when($dateCol && $from, fn($q)=>$q->whereDate($dateCol,'>=',$from))
                    ->when($dateCol && $to,   fn($q)=>$q->whereDate($dateCol,'<=',$to));

                $pending  = $statusCol ? (clone $base)->where($statusCol,0)->count() : 0;
                $resolved = $statusCol ? (clone $base)->where($statusCol,2)->count() : 0;
                $total    = (clone $base)->count();

                $out[] = [
                    'Ward'      => $w->ward ?? $w->unique_id,
                    'Department'=> $d->department ?? $d->unique_id,
                    'Pending'   => $pending,
                    'Resolved'  => $resolved,
                    'Total'     => $total,
                ];
            }
        }

        $filename = 'wardwise_report_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($out) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Ward','Department','Pending','Resolved','Total']);
            foreach ($out as $row) fputcsv($fh, $row);
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* Helper */
    private function pickCol(string $table, array $cands)
    {
        foreach ($cands as $c) {
            if (\Schema::hasColumn($table,$c)) return $c;
        }
        return null;
    }
}
