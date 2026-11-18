@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* btn style */
  .btn-right {
    float: right;
  }

  /* Screen print area hide-everything-except-table (kept) */
  @media print {
    body * {
      visibility: hidden;
    }

    #printable,
    #printable * {
      visibility: visible;
    }

    #printable {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
  }
</style>

<?php $get_zone_name = get_zone(); ?>

@php
// Pull ALL sources (even if there are no complaint entries)
$all_sources = \Illuminate\Support\Facades\DB::table('source_name')
->when(\Illuminate\Support\Facades\Schema::hasColumn('source_name','is_delete'), function($q){
$q->where('is_delete', 0);
})
->orderBy('source_name')
->get(['unique_id','source_name']);

// Identify column names present in new_complaints (robust across schemas)
$dateCol = null;
foreach (['created_at','date','occur_time','complaint_date'] as $c) {
if (\Illuminate\Support\Facades\Schema::hasColumn('new_complaints', $c)) { $dateCol = $c; break; }
}
$zoneCol = null;
foreach (['zone','zone_id'] as $c) {
if (\Illuminate\Support\Facades\Schema::hasColumn('new_complaints', $c)) { $zoneCol = $c; break; }
}
$sourceCol = null;
foreach (['source','source_id','source_name'] as $c) {
if (\Illuminate\Support\Facades\Schema::hasColumn('new_complaints', $c)) { $sourceCol = $c; break; }
}
$statusCol = null;
foreach (['status','complaint_status'] as $c) {
if (\Illuminate\Support\Facades\Schema::hasColumn('new_complaints', $c)) { $statusCol = $c; break; }
}
$hasIsDelete = \Illuminate\Support\Facades\Schema::hasColumn('new_complaints','is_delete');

$from = request('from_date');
$to = request('to_date');
@endphp

<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4 class="">
      <span class="text-muted fw-light">Report /</span> Zone and Complaint Source Wise Report
    </h4>
  </div>
</div>

<div class="row g-2 align-items-end mb-3">
  <div class="col-md-2">
    <label class="form-label">From Date</label>
    <input type="date" id="from_date" class="form-control" value="{{ request('from_date') }}">
  </div>

  <div class="col-md-2">
    <label class="form-label">To Date</label>
    <input type="date" id="to_date" class="form-control" value="{{ request('to_date') }}">
  </div>

  <div class="col-md-2">
    <label class="form-label">Zone</label>
    <select class="form-select" id="zone" name="zone">
      <option value="">Select Zone</option>
      @foreach ($get_zone_name as $get_zone)
      <option value="{{ $get_zone->unique_id }}" {{ request('zone') == $get_zone->unique_id ? 'selected' : '' }}>
        {{ $get_zone->zone }}
      </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-1">
    <button id="filter_btn" class="btn btn-success w-100">
      <i class="bi bi-search"></i> Go
    </button>
  </div>

  <div class="col-md-5 d-flex justify-content-end gap-2">
    <button id="print_btn" class="btn btn-outline-primary">
      <i class="bi bi-printer"></i> Print
    </button>
    <button id="excel_btn" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel"></i> Excel
    </button>
  </div>
</div>

<div id="printable" class="card">
  <div class="card-header"></div>
  <div class="card-body">
    <table class="table table-bordered text-center align-middle" id="zoneSourceTable">
      <thead>
        <tr>
          <th>Zone</th>
          <th>Complaint Source</th>
          <th>Pending</th>
          <th>Resolved</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($report as $zoneBlock)
        @continue(request('zone') && ($zoneBlock['zone_id'] != request('zone')))

        @php
        $rows = $zoneBlock['rows'] ?? [];
        $bySource = [];
        foreach ($rows as $r) { $bySource[$r['source_id']] = $r; }

        $rowspan = max(1, $all_sources->count()) + 1;
        $printedZoneCell = false;

        $totPending = 0;
        $totResolved = 0;
        $totTotal = 0;
        @endphp

        @foreach($all_sources as $src)
        @php
        $base = \Illuminate\Support\Facades\DB::table('new_complaints');

        if ($zoneCol) { $base = $base->where($zoneCol, $zoneBlock['zone_id']); }
        if ($sourceCol) { $base = $base->where($sourceCol, $src->unique_id); }
        if ($hasIsDelete) { $base = $base->where('is_delete', 0); }
        if ($dateCol) {
        if ($from) { $base = $base->whereDate($dateCol, '>=', $from); }
        if ($to) { $base = $base->whereDate($dateCol, '<=', $to); }
          }

          $pending=$statusCol ? (clone $base)->where($statusCol, 0)->count() : 0;
          $resolved = $statusCol ? (clone $base)->where($statusCol, 2)->count() : 0;
          $total = (clone $base)->count();

          $totPending += (int)$pending;
          $totResolved += (int)$resolved;
          $totTotal += (int)$total;

          $label = $src->source_name;
          $sid = $src->unique_id;
          @endphp
          <tr>
            @if(!$printedZoneCell)
            <td rowspan="{{ $rowspan }}">{{ $zoneBlock['zone_name'] }}</td>
            @php $printedZoneCell = true; @endphp
            @endif
            <td class="text-start">{{ $label }}</td>
            <td>{{ $pending }}</td>
            <td>{{ $resolved }}</td>
            <td>{{ $total }}</td>
            <td>
              <a href="javascript:void(0)"
                class="btn-view text-decoration-none"
                title="View details"
                data-zone-id="{{ $zoneBlock['zone_id'] }}"
                data-zone-name="{{ $zoneBlock['zone_name'] }}"
                data-source-id="{{ $sid }}"
                data-source-name="{{ $label }}">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
          @endforeach

          <tr class="fw-bold bg-light">
            <td class="text-end">Total</td>
            <td class="text-center">{{ $totPending }}</td>
            <td class="text-center">{{ $totResolved }}</td>
            <td class="text-center">{{ $totTotal }}</td>
            <td>
              <a href="javascript:void(0)"
                class="btn-view text-decoration-none"
                title="View details"
                data-zone-id="{{ $zoneBlock['zone_id'] }}"
                data-zone-name="{{ $zoneBlock['zone_name'] }}"
                data-source-id=""
                data-source-name="All Sources">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-muted text-center">No data</td>
          </tr>
          @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- ===== Status Details Modal ===== -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="statusModalBody">
          <div class="text-center text-muted">Loading…</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ✅ Filter (date + zone): reload current page with query params
document.getElementById('filter_btn')?.addEventListener('click', function() {
  const from = document.getElementById('from_date')?.value || '';
  const to   = document.getElementById('to_date')?.value || '';
  const zone = document.getElementById('zone')?.value || '';

  const params = new URLSearchParams(window.location.search);

  if (from) { params.set('from_date', from); } else { params.delete('from_date'); }
  if (to)   { params.set('to_date', to);     } else { params.delete('to_date'); }
  if (zone) { params.set('zone', zone);      } else { params.delete('zone'); }

  const base = window.location.pathname;
  const qs = params.toString();
  window.location.href = qs ? `${base}?${qs}` : base;
});

// ✅ Export to Excel
document.getElementById('excel_btn')?.addEventListener('click', function() {
  const table = document.getElementById('zoneSourceTable');
  if (!table) return;

  const cloned = table.cloneNode(true);

  const html =
    `<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<!--[if gte mso 9]>
<xml>
  <o:ExcelWorkbook>
    <o:ExcelWorksheets>
      <o:ExcelWorksheet>
        <o:Name>Zone_Source_Report</o:Name>
        <o:WorksheetOptions>
          <o:DisplayGridlines/>
        </o:WorksheetOptions>
      </o:ExcelWorksheet>
    </o:ExcelWorksheets>
  </o:ExcelWorkbook>
</xml>
<![endif]-->
</head>
<body>
<h3 style="text-align:center;">Zone &amp; Source Report</h3>
${cloned.outerHTML}
</body>`;

  const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'Zone_Source_Report.xls';
  document.body.appendChild(link);
  link.click();
  setTimeout(() => {
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }, 0);
});

// ✅ Redirect to print.blade.php with current filters
document.getElementById('print_btn')?.addEventListener('click', function() {
  const from = document.getElementById('from_date')?.value || '';
  const to   = document.getElementById('to_date')?.value || '';
  const zone = document.getElementById('zone')?.value || '';

  const params = new URLSearchParams();
  if (from) params.set('from_date', from);
  if (to) params.set('to_date', to);
  if (zone) params.set('zone', zone);

  window.location.href = `{{ route('zonecomplaint.print') }}?${params.toString()}`;
});
</script>

@endsection
