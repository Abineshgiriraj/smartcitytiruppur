@extends('layouts/contentNavbarLayout')

@section('title', 'Ward Wise Complaint Report')

@php
  // Filters
  $from = request('from_date');
  $to   = request('to_date');
  $wardFilter = request('ward');

  // Dynamic column resolution
  $dateCol    = null; foreach (['created_at','date','occur_time','complaint_date'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $dateCol=$c; break; } }
  $deptCol    = null; foreach (['department','dept','dept_id','department_id'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $deptCol=$c; break; } }
  $wardCol    = null; foreach (['ward','ward_id','wardid','ward_unique_id'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $wardCol=$c; break; } }
  $statusCol  = null; foreach (['status','complaint_status'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $statusCol=$c; break; } }
  $hasIsDelete = Schema::hasColumn('new_complaints','is_delete');

  // Wards for dropdown
  $get_ward_name = get_ward(); // expects unique_id, ward

  // ✅ All departments (list all 14 always)
  $all_departments = \Illuminate\Support\Facades\DB::table('department')
                      ->when(\Illuminate\Support\Facades\Schema::hasColumn('department','is_delete'), fn($q)=>$q->where('is_delete',0))
                      ->when(\Illuminate\Support\Facades\Schema::hasColumn('department','is_active'), fn($q)=>$q->where('is_active',1))
                      ->orderBy('department')
                      ->get(['unique_id','department']);
@endphp

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  /* Header row light grey like reference */
  #wardReportTable thead th{
    background:#f1f3f5 !important;
    color:#212529;
  }
  /* Keep cells plain */
  .table > :not(caption) > * > *{ background-color:#fff !important; color:#212529; }

  /* Total row style */
  .row-total { background:#eee !important; font-weight:600; }

  /* Outline button look (like your dept-wise) */
  .btn-outline-primary{
    --bs-btn-color:#0d6efd;
    --bs-btn-border-color:#0d6efd;
    --bs-btn-hover-bg:#0d6efd;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-border-color:#0d6efd;
    --bs-btn-active-bg:#0d6efd;
    --bs-btn-active-border-color:#0d6efd;
  }
  .btn-outline-success{
    --bs-btn-color:#198754;
    --bs-btn-border-color:#198754;
    --bs-btn-hover-bg:#198754;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-border-color:#198754;
    --bs-btn-active-bg:#198754;
    --bs-btn-active-border-color:#198754;
  }
</style>

<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Report /</span> Ward Wise Report</h4>
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
  <div class="col-md-3">
    <label class="form-label">Ward</label>
    <select id="ward" class="form-select">
      <option value="">Select Ward</option>
      @foreach ($get_ward_name as $w)
        <option value="{{ $w->unique_id }}" {{ request('ward') == $w->unique_id ? 'selected' : '' }}>
          {{ $w->ward }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-1">
    <button id="filter_btn" type="button" class="btn btn-success w-100">
      <i class="bi bi-search"></i> Go
    </button>
  </div>
  <!-- ✅ PRINT + EXCEL buttons right-aligned -->
  <div class="col-md-4 d-flex justify-content-end gap-2">
    <button id="print_btn" type="button" class="btn btn-outline-primary">
      <i class="bi bi-printer"></i> Print
    </button>
    <button id="excel_btn" type="button" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel"></i> Excel
    </button>
  </div>
</div>

<div id="printable" class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="wardReportTable" class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Ward</th>
            <th>Department</th>
            <th>Pending</th>
            <th>Resolved</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        @php
          // Decide which wards to show
          $wardsToShow = [];
          if ($wardFilter) {
            $label = \DB::table('ward')->where('unique_id',$wardFilter)->value('ward') ?? $wardFilter;
            $wardsToShow[] = (object)['unique_id'=>$wardFilter,'ward'=>$label];
          } else {
            $wardsToShow = $get_ward_name;
          }
        @endphp

        @forelse ($wardsToShow as $wardRow)
          @php
            // For each ward, compute rows for ALL departments (including zero counts)
            $deptRows = [];
            $totals = ['pending'=>0,'resolved'=>0,'total'=>0];

            foreach ($all_departments as $d) {
              // Base query with filters
              $base = \DB::table('new_complaints')
                ->when($hasIsDelete, fn($q)=>$q->where('is_delete',0))
                ->when($wardCol, fn($q)=>$q->where($wardCol, $wardRow->unique_id))
                ->when($deptCol, fn($q)=>$q->where($deptCol, $d->unique_id))
                ->when($dateCol && $from, fn($q)=>$q->whereDate($dateCol, '>=', $from))
                ->when($dateCol && $to,   fn($q)=>$q->whereDate($dateCol, '<=', $to));

              $pending  = $statusCol ? (clone $base)->where($statusCol,0)->count() : 0;
              $resolved = $statusCol ? (clone $base)->where($statusCol,2)->count() : 0;
              $total    = (clone $base)->count();

              $deptRows[] = [
                'dept_id'   => $d->unique_id,
                'dept_name' => $d->department,
                'pending'   => $pending,
                'resolved'  => $resolved,
                'total'     => $total,
              ];

              $totals['pending']  += $pending;
              $totals['resolved'] += $resolved;
              $totals['total']    += $total;
            }

            // Rowspan = number of department rows + 1 (for total row)
            $rowspan = max(1, count($deptRows) + 1);
            $firstRow = true;
          @endphp

          @foreach ($deptRows as $r)
            <tr class="data-row">
              @if ($firstRow)
                <td class="text-start fw-semibold" rowspan="{{ $rowspan }}">{{ $wardRow->ward ?? '-' }}</td>
                @php $firstRow = false; @endphp
              @endif
              <td class="text-start">{{ $r['dept_name'] }}</td>
              <td>{{ $r['pending'] }}</td>
              <td>{{ $r['resolved'] }}</td>
              <td>{{ $r['total'] }}</td>
              <td>
                <a href="javascript:void(0)"
                   class="btn-view text-decoration-none"
                   title="View details"
                   data-ward-id="{{ $wardRow->unique_id }}"
                   data-ward-name="{{ $wardRow->ward ?? 'Ward' }}"
                   data-dept-id="{{ $r['dept_id'] }}"
                   data-dept-name="{{ $r['dept_name'] }}">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          @endforeach

          {{-- Total row for the ward (covered by rowspan in Ward column) --}}
          <tr class="row-total">
            <td class="text-start">Total</td>
            <td>{{ $totals['pending'] }}</td>
            <td>{{ $totals['resolved'] }}</td>
            <td>{{ $totals['total'] }}</td>
            <td>—</td>
          </tr>
        @empty
          <tr class="data-row">
            <td colspan="6" class="text-center text-muted">No data</td>
          </tr>
        @endforelse

        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="wardDetailModal" tabindex="-1" aria-labelledby="wardDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="wardDetailModalLabel">Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="wardDetailBody"><div class="text-center text-muted">Loading…</div></div>
      </div>
    </div>
  </div>
</div>

<script>
/* ✅ After using date filters once, clear them on refresh (same idea as dept-wise) */
(function () {
  try {
    var url = new URL(window.location.href);
    var hasDates = url.searchParams.has('from_date') || url.searchParams.has('to_date');
    var marker = localStorage.getItem('wardwise_allow_one_round_with_dates');

    if (hasDates) {
      if (marker === 'armed') {
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        localStorage.removeItem('wardwise_allow_one_round_with_dates');
        var clean = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '') + url.hash;
        window.location.replace(clean);
      } else if (marker === '1') {
        localStorage.setItem('wardwise_allow_one_round_with_dates', 'armed');
      } else {
        localStorage.setItem('wardwise_allow_one_round_with_dates', 'armed');
      }
    } else {
      localStorage.removeItem('wardwise_allow_one_round_with_dates');
    }
  } catch (e) {}
})();
</script>

<script>
  // Filter submit (route preserved as in your file)
  document.getElementById('filter_btn').addEventListener('click', function(){
    try { localStorage.setItem('wardwise_allow_one_round_with_dates', '1'); } catch(e){}
    const p = new URLSearchParams();
    const f = document.getElementById('from_date').value;
    const t = document.getElementById('to_date').value;
    const w = document.getElementById('ward').value;
    if (f) p.set('from_date', f);
    if (t) p.set('to_date', t);
    if (w) p.set('ward', w);
    window.location = "{{ route('wardwisereport') }}" + (p.toString() ? ('?' + p.toString()) : '');
  });

  // Eye icon → AJAX modal (Ward + Dept + Dates)
  document.addEventListener('click', function(e){
    const a = e.target.closest('.btn-view'); if(!a) return;

    const wardId = a.getAttribute('data-ward-id') || '';
    const wardNm = a.getAttribute('data-ward-name') || 'Ward';
    const deptId = a.getAttribute('data-dept-id') || '';
    const deptNm = a.getAttribute('data-dept-name') || 'Department';
    const f = document.getElementById('from_date').value || '';
    const t = document.getElementById('to_date').value || '';

    document.getElementById('wardDetailModalLabel').innerText = `Ward - ${wardNm} - ${deptNm}`;
    const bodyEl = document.getElementById('wardDetailBody');
    bodyEl.innerHTML = '<div class="text-center text-muted">Loading…</div>';

    const url = new URL("{{ route('wardwisereport.details') }}", window.location.origin);
    if (wardId) url.searchParams.set('ward', wardId);
    if (deptId) url.searchParams.set('department', deptId);
    if (f) url.searchParams.set('from_date', f);
    if (t) url.searchParams.set('to_date', t);

    fetch(url.toString(), { headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(r=>r.text()).then(html=>{
        bodyEl.innerHTML = html;
        // Re-execute any scripts from partial
        Array.from(bodyEl.querySelectorAll('script')).forEach(old=>{
          const s=document.createElement('script');
          if (old.src) s.src=old.src; else s.textContent=old.textContent;
          document.body.appendChild(s); document.body.removeChild(s);
        });
      })
      .catch(()=>{ bodyEl.innerHTML = '<div class="text-danger">Failed to load.</div>'; });

    new bootstrap.Modal(document.getElementById('wardDetailModal')).show();
  });

  // ======= PRINT (hide Status col) =======
  function makePrintableTableHTML() {
    const source = document.getElementById('wardReportTable');
    const clone = source.cloneNode(true);
    const ths = clone.querySelectorAll('thead th');
    let statusIdx = -1;
    ths.forEach((th, idx) => { if (th.textContent.trim().toLowerCase() === 'status') statusIdx = idx; });
    if (statusIdx !== -1) {
      ths[statusIdx].remove();
      clone.querySelectorAll('tbody tr').forEach(tr => {
        const cells = tr.querySelectorAll('td');
        if (cells.length > statusIdx) cells[statusIdx].remove();
      });
    }
    return clone.outerHTML;
  }

  document.getElementById('print_btn').addEventListener('click', function() {
    const tableOnly = makePrintableTableHTML();
    const iframe = document.createElement('iframe');
    iframe.style.position='fixed'; iframe.style.right='0'; iframe.style.bottom='0';
    iframe.style.width='0'; iframe.style.height='0'; iframe.style.border='0';
    document.body.appendChild(iframe);
    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(`
    <html>
    <head><title>Ward Wise Report</title><meta charset="utf-8">
      <style>@page{size:A4;margin:12mm}body{font-family:Arial,sans-serif}
      table{width:100%;border-collapse:collapse;font-size:12px}
      th,td{border:1px solid #000;padding:6px;text-align:center}
      thead tr th{font-weight:bold}</style>
    </head><body>${tableOnly}</body>
    /html>`
  );
    doc.close();
    iframe.onload=function(){iframe.contentWindow.focus();setTimeout(function(){iframe.contentWindow.print();iframe.contentWindow.onafterprint=function(){document.body.removeChild(iframe);};},100);};
  });

  // ======= EXCEL (CSV) =======
  document.getElementById('excel_btn').addEventListener('click', function() {
    const params = new URLSearchParams();
    const f = document.getElementById('from_date').value;
    const t = document.getElementById('to_date').value;
    const w = document.getElementById('ward').value;
    if (f) params.set('from_date', f);
    if (t) params.set('to_date', t);
    if (w) params.set('ward', w);
    window.location = "{{ route('wardwisereport.export') }}" + (params.toString() ? ('?' + params.toString()) : '');
  });
</script>
@endsection
