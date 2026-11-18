<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class newstreetlightreportController extends Controller
{
    // List page
    public function index(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $tableName = 'newstreetlight_report';
        $dateCol = $this->pickCol($tableName, ['created_at','complaint_date','date','registered_on','occur_time']);

        $hasZoneId    = Schema::hasColumn($tableName, 'zone');
        $hasWardId    = Schema::hasColumn($tableName, 'ward');
        $hasSourceId  = Schema::hasColumn($tableName, 'source_name');
        $hasStatusCol = Schema::hasColumn($tableName, 'status');
        $hasCreatedAt = Schema::hasColumn($tableName, 'created_at');

        $q = DB::table($tableName . ' as v');

        if ($hasZoneId && Schema::hasColumn('zone','unique_id')) $q->leftJoin('zone as z', 'z.unique_id', '=', 'v.zone');
        if ($hasWardId && Schema::hasColumn('ward','unique_id')) $q->leftJoin('ward as w', 'w.unique_id', '=', 'v.ward');
        if ($hasSourceId && Schema::hasColumn('source_name','unique_id')) $q->leftJoin('source_name as sn', 'sn.unique_id', '=', 'v.source_name');

        if ($dateCol && $from) $q->whereDate("v.$dateCol", '>=', $from);
        if ($dateCol && $to)   $q->whereDate("v.$dateCol", '<=', $to);

        $nameExpr   = $this->safeCoalesceSelect($tableName, ['name','applicant_name','citizen_name'], 'name', "'-'");
        $mobileExpr = $this->safeCoalesceSelect($tableName, ['mobile_no','mobile','phone'], 'mobile_no', "'-'");
        $addrExpr   = $this->safeCoalesceSelect($tableName, ['address1','address'], 'address1', "'-'");

        $zoneExpr   = $hasZoneId ? "COALESCE(z.zone, v.zone, '-') as zone_label" : "COALESCE(v.zone, '-') as zone_label";
        $wardExpr   = $hasWardId ? "COALESCE(w.ward, v.ward, '-') as ward_label" : "COALESCE(v.ward, '-') as ward_label";
        $sourceExpr = $hasSourceId ? "COALESCE(sn.source_name, v.source_name, '-') as source_label" : "COALESCE(v.source_name, '-') as source_label";
        $statusExpr = $hasStatusCol ? "COALESCE(v.status, 0) as status" : "0 as status";

        $orderCol = $dateCol ? "v.$dateCol" : (Schema::hasColumn($tableName,'grievance_id') ? 'v.grievance_id' : null);

        $rows = $q->select([
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
                ->limit(5000)
                ->get();

        return view('content.newstreetlightreport.list', compact('rows','from','to'));
    }

    // Export CSV
    public function export(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $tableName = 'newstreetlight_report';
        $dateCol = $this->pickCol($tableName, ['created_at','complaint_date','date','registered_on','occur_time']);

        $hasZoneId    = Schema::hasColumn($tableName, 'zone');
        $hasWardId    = Schema::hasColumn($tableName, 'ward');
        $hasSourceId  = Schema::hasColumn($tableName, 'source_name');
        $hasStatusCol = Schema::hasColumn($tableName, 'status');
        $hasCreatedAt = Schema::hasColumn($tableName, 'created_at');

        $q = DB::table($tableName . ' as v');

        if ($hasZoneId && Schema::hasColumn('zone','unique_id')) $q->leftJoin('zone as z', 'z.unique_id', '=', 'v.zone');
        if ($hasWardId && Schema::hasColumn('ward','unique_id')) $q->leftJoin('ward as w', 'w.unique_id', '=', 'v.ward');
        if ($hasSourceId && Schema::hasColumn('source_name','unique_id')) $q->leftJoin('source_name as sn', 'sn.unique_id', '=', 'v.source_name');

        if ($dateCol && $from) $q->whereDate("v.$dateCol", '>=', $from);
        if ($dateCol && $to)   $q->whereDate("v.$dateCol", '<=', $to);

        $nameExpr   = $this->safeCoalesceSelect($tableName, ['name','applicant_name','citizen_name'], 'name', "'-'");
        $mobileExpr = $this->safeCoalesceSelect($tableName, ['mobile_no','mobile','phone'], 'mobile_no', "'-'");
        $addrExpr   = $this->safeCoalesceSelect($tableName, ['address1','address'], 'address1', "'-'");

        $zoneExpr   = $hasZoneId ? "COALESCE(z.zone, v.zone, '-') as zone_label" : "COALESCE(v.zone, '-') as zone_label";
        $wardExpr   = $hasWardId ? "COALESCE(w.ward, v.ward, '-') as ward_label" : "COALESCE(v.ward, '-') as ward_label";
        $sourceExpr = $hasSourceId ? "COALESCE(sn.source_name, v.source_name, '-') as source_label" : "COALESCE(v.source_name, '-') as source_label";
        $statusExpr = $hasStatusCol ? "COALESCE(v.status, 0) as status" : "0 as status";

        $orderCol = $dateCol ? "v.$dateCol" : (Schema::hasColumn($tableName,'grievance_id') ? 'v.grievance_id' : null);

        $data = $q->select([
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
                ->get();

        $filename = 'newstreetlight_report_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['S.No','Complaint ID','Name','Mobile No','Address','Zone','Ward','Source Name','Status']);
            $i = 1;
            foreach ($data as $r) {
                $complaintId = trim(($r->grievance_prefix ?? '') . ($r->grievance_id ?? ''));
                fputcsv($fh, [
                    $i++,
                    $complaintId ?: '-',
                    $r->name ?? '-',
                    $r->mobile_no ?? '-',
                    $r->address1 ?? '-',
                    $r->zone_label ?? '-',
                    $r->ward_label ?? '-',
                    $r->source_label ?? '-',
                    ((int)($r->status ?? 0) === 0) ? 'Pending' : 'Resolved',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // PRINT page
    public function print(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');
        $tableName = 'newstreetlight_report';
        $dateCol = $this->pickCol($tableName, ['created_at','complaint_date','date','registered_on','occur_time']);

        $hasZoneId    = Schema::hasColumn($tableName, 'zone');
        $hasWardId    = Schema::hasColumn($tableName, 'ward');
        $hasSourceId  = Schema::hasColumn($tableName, 'source_name');
        $hasStatusCol = Schema::hasColumn($tableName, 'status');
        $hasCreatedAt = Schema::hasColumn($tableName, 'created_at');

        $q = DB::table($tableName . ' as v');

        if ($hasZoneId && Schema::hasColumn('zone','unique_id')) $q->leftJoin('zone as z', 'z.unique_id', '=', 'v.zone');
        if ($hasWardId && Schema::hasColumn('ward','unique_id')) $q->leftJoin('ward as w', 'w.unique_id', '=', 'v.ward');
        if ($hasSourceId && Schema::hasColumn('source_name','unique_id')) $q->leftJoin('source_name as sn', 'sn.unique_id', '=', 'v.source_name');

        if ($dateCol && $from) $q->whereDate("v.$dateCol", '>=', $from);
        if ($dateCol && $to)   $q->whereDate("v.$dateCol", '<=', $to);

        $nameExpr   = $this->safeCoalesceSelect($tableName, ['name','applicant_name','citizen_name'], 'name', "'-'");
        $mobileExpr = $this->safeCoalesceSelect($tableName, ['mobile_no','mobile','phone'], 'mobile_no', "'-'");
        $addrExpr   = $this->safeCoalesceSelect($tableName, ['address1','address'], 'address1', "'-'");

        $zoneExpr   = $hasZoneId ? "COALESCE(z.zone, v.zone, '-') as zone_label" : "COALESCE(v.zone, '-') as zone_label";
        $wardExpr   = $hasWardId ? "COALESCE(w.ward, v.ward, '-') as ward_label" : "COALESCE(v.ward, '-') as ward_label";
        $sourceExpr = $hasSourceId ? "COALESCE(sn.source_name, v.source_name, '-') as source_label" : "COALESCE(v.source_name, '-') as source_label";
        $statusExpr = $hasStatusCol ? "COALESCE(v.status, 0) as status" : "0 as status";

        $orderCol = $dateCol ? "v.$dateCol" : (Schema::hasColumn($tableName,'grievance_id') ? 'v.grievance_id' : null);

        $rows = $q->select([
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
        ])->when($orderCol, fn($qq)=>$qq->orderByDesc(DB::raw($orderCol)))
          ->get();

        return view('content.newstreetlightreport.print', compact('rows','from','to'));
    }

    // Helper methods
    private function pickCol(string $table, array $cands)
    {
        foreach ($cands as $c) {
            if (Schema::hasColumn($table, $c)) return $c;
        }
        return null;
    }

    private function safeCoalesceSelect(string $table, array $cands, string $alias, string $fallback = "'-'")
    {
        $parts = [];
        foreach ($cands as $c) {
            if (Schema::hasColumn($table, $c)) $parts[] = "v.$c";
        }
        if (empty($parts)) return "$fallback as $alias";
        return 'COALESCE(' . implode(', ', $parts) . ", $fallback) as $alias";
    }
}
