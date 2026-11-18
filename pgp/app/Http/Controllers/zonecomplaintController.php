<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class zonecomplaintController extends Controller
{
    /** LIST PAGE */
    public function index()
    {
        $from       = request('from_date');
        $to         = request('to_date');
        $zoneFilter = request('zone');

        $report = $this->buildReport($from, $to, $zoneFilter);

        if (request()->ajax() && request()->has('zones_dt')) {
            $data = DB::table('zone')->where('is_delete', 0)->get();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('zone.edit', $row->unique_id);
                    $btn  = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.zonecomplaint.list', ['report' => $report]);
    }

    /** PRINT PAGE */
   public function print(Request $request)
{
    $from       = $request->query('from_date');
    $to         = $request->query('to_date');
    $zoneFilter = $request->query('zone');

    $report = $this->buildReport($from, $to, $zoneFilter);

    // Fetch all complaint sources
    $all_sources = DB::table('source_name')->where('is_delete', 0)->get();

    return view('content.zonecomplaint.print', [
        'report'      => $report,
        'all_sources' => $all_sources,
        'from'        => $from,
        'to'          => $to,
        'zoneFilter'  => $zoneFilter
    ]);
}

    /** CSV Export (respects filters) */
    public function export(Request $request): StreamedResponse
    {
        $from       = $request->query('from_date');
        $to         = $request->query('to_date');
        $zoneFilter = $request->query('zone');

        $report = $this->buildReport($from, $to, $zoneFilter);
        $filename = 'zone_source_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($out, ['Zone', 'Complaint Source', 'New', 'Pending', 'Resolved', 'Total']);

            foreach ($report as $zoneBlock) {
                $zoneName = $zoneBlock['zone_name'];

                if (empty($zoneBlock['rows'])) {
                    fputcsv($out, [$zoneName, '-', 0, 0, 0, 0]);
                    fputcsv($out, ['Total', '', 0, 0, 0, 0]);
                    continue;
                }

                foreach ($zoneBlock['rows'] as $r) {
                    fputcsv($out, [
                        $zoneName,
                        $r['source_name'],
                        $r['new'],
                        $r['pending'],
                        $r['resolved'],
                        $r['total'],
                    ]);
                    $zoneName = '';
                }

                fputcsv($out, [
                    'Total', '',
                    $zoneBlock['totals']['new'],
                    $zoneBlock['totals']['pending'],
                    $zoneBlock['totals']['resolved'],
                    $zoneBlock['totals']['total'],
                ]);
            }

            fclose($out);
        }, $filename, $headers);
    }

    /** AJAX for Modal – filtered list per Zone/Source */
    public function details(Request $request)
    {
        $from   = $request->query('from_date');
        $to     = $request->query('to_date');
        $zoneId = $request->query('zone');
        $srcId  = $request->query('source');

        $q = DB::table('new_complaints as nc')
            ->leftJoin('source_name as s', 'nc.source_name', '=', 's.unique_id')
            ->leftJoin('zone as z', 'nc.zone', '=', 'z.unique_id')
            ->where('nc.is_delete', 0);

        if ($zoneId) { $q->where('nc.zone', $zoneId); }
        if ($srcId)  { $q->where('nc.source_name', $srcId); }
        if ($from)   { $q->whereDate('nc.created_at', '>=', $from); }
        if ($to)     { $q->whereDate('nc.created_at', '<=', $to); }

        $items = $q->orderBy('nc.created_at', 'desc')
            ->select(
                'nc.unique_id as id',
                's.source_name',
                'z.zone',
                'nc.status',
                'nc.created_at'
            )
            ->limit(500)
            ->get();

        $html  = '<div class="table-responsive">';
        $html .= '<table class="table table-sm table-bordered align-middle">';
        $html .= '<thead class="table-light"><tr>';
        $html .= '<th style="width:70px">S.No</th><th>Source</th><th>Status</th><th>Date</th>';
        $html .= '</tr></thead><tbody>';

        if ($items->isEmpty()) {
            $html .= '<tr><td colspan="4" class="text-center text-muted">No complaints found.</td></tr>';
        } else {
            $i = 1;
            foreach ($items as $row) {
                $statusRaw = $row->status;
                if (is_numeric($statusRaw)) {
                    $statusInt = (int)$statusRaw;
                    if ($statusInt === 0)      $status = 'Pending';
                    elseif ($statusInt === 2)  $status = 'Completed';
                    else                        $status = (string)$statusRaw;
                } else {
                    $status = (string)$statusRaw;
                }

                $dateStr = $row->created_at ? date('Y-m-d', strtotime($row->created_at)) : '';

                $html .= '<tr>';
                $html .= '<td>'.($i++).'</td>';
                $html .= '<td>'.e($row->source_name ?? '-').'</td>';
                $html .= '<td>'.e($status).'</td>';
                $html .= '<td>'.e($dateStr).'</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        return response($html);
    }

    /** Build Zone × Source report with totals */
    private function buildReport(?string $from, ?string $to, ?string $zoneFilter): array
    {
        $q = DB::table('new_complaints as nc')
            ->join('zone as z', 'nc.zone', '=', 'z.unique_id')
            ->join('source_name as s', 'nc.source_name', '=', 's.unique_id')
            ->where('nc.is_delete', 0)
            ->where('z.is_delete', 0);

        if ($from)       $q->whereDate('nc.created_at', '>=', $from);
        if ($to)         $q->whereDate('nc.created_at', '<=', $to);
        if ($zoneFilter) $q->where('nc.zone', $zoneFilter);

        $rows = $q->select(
                'z.unique_id as zone_id',
                'z.zone as zone_name',
                's.unique_id as source_id',
                's.source_name as source_name',
                DB::raw("SUM(CASE WHEN nc.status IN ('New','Registered','Fresh') THEN 1 ELSE 0 END) AS new_cnt"),
                DB::raw("SUM(CASE WHEN nc.status IN ('Pending','In-Progress') THEN 1 ELSE 0 END) AS pending_cnt"),
                DB::raw("SUM(CASE WHEN nc.status IN ('Resolved','Completed','Closed') THEN 1 ELSE 0 END) AS resolved_cnt"),
                DB::raw("COUNT(*) AS total_cnt")
            )
            ->groupBy('z.unique_id', 'z.zone', 's.unique_id', 's.source_name')
            ->orderBy('z.zone')
            ->orderBy('s.source_name')
            ->get();

        $byZone = [];
        foreach ($rows as $r) {
            $zid = $r->zone_id;
            if (!isset($byZone[$zid])) {
                $byZone[$zid] = [
                    'zone_id'   => $zid,
                    'zone_name' => $r->zone_name,
                    'rows'      => [],
                    'totals'    => ['new' => 0, 'pending' => 0, 'resolved' => 0, 'total' => 0],
                ];
            }
            $byZone[$zid]['rows'][] = [
                'source_id'   => $r->source_id,
                'source_name' => $r->source_name,
                'new'         => (int)$r->new_cnt,
                'pending'     => (int)$r->pending_cnt,
                'resolved'    => (int)$r->resolved_cnt,
                'total'       => (int)$r->total_cnt,
            ];
            $byZone[$zid]['totals']['new']     += (int)$r->new_cnt;
            $byZone[$zid]['totals']['pending'] += (int)$r->pending_cnt;
            $byZone[$zid]['totals']['resolved']+= (int)$r->resolved_cnt;
            $byZone[$zid]['totals']['total']   += (int)$r->total_cnt;
        }

        $allZones = DB::table('zone')->where('is_delete', 0)->get(['unique_id','zone']);
        foreach ($allZones as $z) {
            if (!isset($byZone[$z->unique_id])) {
                $byZone[$z->unique_id] = [
                    'zone_id'   => $z->unique_id,
                    'zone_name' => $z->zone,
                    'rows'      => [],
                    'totals'    => ['new' => 0, 'pending' => 0, 'resolved' => 0, 'total' => 0],
                ];
            }
        }

        $report = array_values($byZone);
        usort($report, fn($a,$b) => strcmp($a['zone_name'], $b['zone_name']));
        return $report;
    }

    /* ===== your existing CRUD (unchanged) ===== */

    public function add() { return view('content.zone.form'); }

    public function store(Request $request)
    {
        $request->validate([
            'zone'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $zone = ZoneCreation::where('zone', $request->zone_name)->where('is_delete', 0)->first();
        if ($zone) { return response()->json(['message' => 'Zone name already exists.'], 422); }

        $zone = new ZoneCreation();
        $zone->zone        = $request->zone;
        $zone->description = $request->description;
        $zone->unique_id   = unique_id();
        $zone->is_delete   = 0;
        $zone->save();

        return response()->json(['message' => 'Zone created successfully.']);
    }

    public function edit($id)
    {
        $zone = ZoneCreation::where('is_delete', 0)->where('unique_id', $id)->first();
        if (!$zone) { return redirect()->back()->with('error', 'zone not found.'); }
        return view('content.zone.form', compact('zone'));
    }

    public function update(Request $request, zone $zone, $id)
    {
        $request->validate([
            'zone'        => 'required|string',
            'description' => 'string',
        ]);

        $zone = ZoneCreation::where('is_delete', 0)->where('unique_id', $id)->first();
        $zone->update(['zone' => $request->zone, 'description' => $request->description]);

        return response()->json(['success' => true, 'message' => 'Zone updated successfully.', 'zone' => $zone]);
    }

    public function destroy($id)
    {
        $zone = ZoneCreation::where('unique_id', $id)->first();
        if (!$zone) { return response()->json(['success' => false, 'message' => 'Category not found.']); }

        $zone->is_delete = 1;
        $zone->save();

        return response()->json(['success' => true, 'message' => 'Deleted successfully!']);
    }
}
