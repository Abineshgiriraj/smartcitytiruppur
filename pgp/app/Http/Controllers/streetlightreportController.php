<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class streetlightreportController extends Controller
{
    // List page
    public function index(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $rows = $this->getRows($from, $to);

        return view()->first([
            'content.streetlightreport.list',
            'content.streetlightreport.index',
            'content.streetlightreport',
        ], compact('rows','from','to'));
    }

    // Excel export (CSV)
    public function export(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $data = $this->getRows($from, $to, 50000);

        $filename = 'streetlight_report_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['S.No','Complaint ID','Name','Mobile No','Address','Zone','Ward','Source Name','Status']);
            $i = 1;
            foreach ($data as $r) {
                $complaintId = trim(($r->grievance_prefix ?? '').($r->grievance_id ?? ''));
                fputcsv($fh, [
                    $i++,
                    $complaintId ?: '-',
                    $r->name ?: '-',
                    $r->mobile_no ?: '-',
                    $r->address1 ?: '-',
                    $r->zone_label ?: '-',
                    $r->ward_label ?: '-',
                    $r->source_label ?: '-',
                    $this->statusText($r->status ?? 0),
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Print page
    public function print(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $rows = $this->getRows($from, $to, 5000);

        return view('content.streetlightreport.print', compact('rows','from','to'));
    }

    /**
     * Centralized row retrieval with optional limit
     */
    private function getRows($from=null, $to=null, $limit=5000)
    {
        $dateCol = $this->pickCol('streetlight_report', [
            'created_at','complaint_date','date','registered_on','occur_time'
        ]);

        $hasZoneId    = \Schema::hasColumn('streetlight_report', 'zone');
        $hasWardId    = \Schema::hasColumn('streetlight_report', 'ward');
        $hasSourceId  = \Schema::hasColumn('streetlight_report', 'source_name');
        $hasStatusCol = \Schema::hasColumn('streetlight_report', 'status');
        $hasCreatedAt = \Schema::hasColumn('streetlight_report', 'created_at');

        $q = DB::table('streetlight_report as v');

        // Optional joins to label tables
        if ($hasZoneId && \Schema::hasColumn('zone','unique_id')) $q->leftJoin('zone as z', 'z.unique_id', '=', 'v.zone');
        if ($hasWardId && \Schema::hasColumn('ward','unique_id')) $q->leftJoin('ward as w', 'w.unique_id', '=', 'v.ward');
        if ($hasSourceId && \Schema::hasColumn('source_name','unique_id')) $q->leftJoin('source_name as sn', 'sn.unique_id', '=', 'v.source_name');

        // Date filter
        if ($dateCol && $from) $q->whereDate("v.$dateCol", '>=', $from);
        if ($dateCol && $to)   $q->whereDate("v.$dateCol", '<=', $to);

        // COALESCE expressions
        $nameExpr   = $this->safeCoalesce('streetlight_report', ['name','applicant_name','citizen_name'], "name", "'-'");
        $mobileExpr = $this->safeCoalesce('streetlight_report', ['mobile_no','mobile','phone'], "mobile_no", "'-'");
        $addrExpr   = $this->safeCoalesce('streetlight_report', ['address1','address'], "address1", "'-'");

        $zoneExpr   = $hasZoneId
                        ? "COALESCE(z.zone, v.zone, '-') as zone_label"
                        : "COALESCE(v.zone, '-') as zone_label";
        $wardExpr   = $hasWardId
                        ? "COALESCE(w.ward, v.ward, '-') as ward_label"
                        : "COALESCE(v.ward, '-') as ward_label";
        $sourceExpr = $hasSourceId
                        ? "COALESCE(sn.source_name, v.source_name, '-') as source_label"
                        : "COALESCE(v.source_name, '-') as source_label";
        $statusExpr = $hasStatusCol
                        ? "COALESCE(v.status, 0) as status"
                        : "0 as status";

        $orderCol = $dateCol ? "v.$dateCol"
                   : (\Schema::hasColumn('streetlight_report','grievance_id') ? 'v.grievance_id' : null);

        return $q->select([
            DB::raw("COALESCE(v.grievance_prefix,'') as grievance_prefix"),
            DB::raw("COALESCE(v.grievance_id,'')   as grievance_id"),
            DB::raw($hasCreatedAt ? "DATE(v.created_at) as entry_date" : "NULL as entry_date"),
            DB::raw($nameExpr),
            DB::raw($mobileExpr),
            DB::raw($addrExpr),
            DB::raw($zoneExpr),
            DB::raw($wardExpr),
            DB::raw($sourceExpr),
            DB::raw($statusExpr),
        ])
        ->when($orderCol, fn($qq) => $qq->orderByDesc(DB::raw($orderCol)))
        ->limit($limit)
        ->get();
    }

    /** Return first existing column name from candidates */
    private function pickCol(string $table, array $cands)
    {
        foreach ($cands as $c) if (\Schema::hasColumn($table,$c)) return $c;
        return null;
    }

    /** Build COALESCE expression using only columns that exist */
    private function safeCoalesce(string $table, array $cands, string $alias, string $fallback = "''")
    {
        $present = [];
        foreach ($cands as $c) {
            if (\Schema::hasColumn($table, $c)) $present[] = "v.$c";
        }
        if (empty($present)) return "$fallback as $alias";
        $expr = 'COALESCE(' . implode(', ', $present) . ", $fallback)";
        return "$expr as $alias";
    }

    private function statusText($code)
    {
        return ((int)$code === 0) ? 'Pending' : 'Resolved';
    }
}
