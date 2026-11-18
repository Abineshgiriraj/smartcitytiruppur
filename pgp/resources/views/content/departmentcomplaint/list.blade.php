@extends('layouts/contentNavbarLayout')

@section('title', 'Department Complaint Wise Report')

<style>
/* Outline button look (blue) */
.btn-outline-primary{
  --bs-btn-color:#0d6efd;
  --bs-btn-border-color:#0d6efd;
  --bs-btn-hover-bg:#0d6efd;
  --bs-btn-hover-color:#fff;
  --bs-btn-hover-border-color:#0d6efd;
  --bs-btn-active-bg:#0d6efd;
  --bs-btn-active-border-color:#0d6efd;
}
#print_btn:not([disabled]){ opacity:1; }

/* Plain table cells (no zebra colors) */
.table > :not(caption) > * > *{
  background-color:#fff !important;
  color:#212529;
}

/* Header row grey */
#deptComplaintTable thead th{
  background:#f1f3f5 !important;
  color:#212529;
}

.total-label{ text-align:right !important; font-weight:600; }

@media print {
  body * { visibility: hidden; }
  #printable, #printable * { visibility: visible; }
  #printable { position: absolute; left: 0; top: 0; width: 100%; }
}

/* Pagination footer */
.table-footer {
  display:flex;
  justify-content:flex-end;
  align-items:center;
  gap:.75rem;
}
.table-footer .pagination{
  margin:0;
}

/* Top toolbar */
.table-toolbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:.5rem;
}
.table-toolbar .form-select{
  width:auto;
}
.table-toolbar .search-input{
  max-width:260px;
}

/* Totals row style */
#deptComplaintTable tfoot td{
  font-weight:600;
  background:#f9f9f9;
}
#deptComplaintTable tfoot td.total-label{
  text-align:right !important;
}
</style>

<?php
$get_group = get_dept_name();
$dateCol    = null; foreach (['created_at','date','occur_time','complaint_date'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $dateCol=$c; break; } }
$deptCol    = null; foreach (['department','dept','dept_id','department_id'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $deptCol=$c; break; } }
$statusCol  = null; foreach (['status','complaint_status'] as $c) { if (Schema::hasColumn('new_complaints',$c)) { $statusCol=$c; break; } }
$hasIsDelete = Schema::hasColumn('new_complaints','is_delete');

$from = request('from_date');
$to   = request('to_date');
$deptFilter = request('department');

/* Header image */
$headerDataUrl = null;
$possiblePublicPath = public_path('resources/content/departmentcomplaint/header.png');
if (file_exists($possiblePublicPath) && is_readable($possiblePublicPath)) {
    $mime = @mime_content_type($possiblePublicPath) ?: 'image/png';
    $data = base64_encode(file_get_contents($possiblePublicPath));
    $headerDataUrl = "data:{$mime};base64,{$data}";
} else {
    $headerDataUrl = asset('resources/content/departmentcomplaint/header.png');
}
$headerDataUrlJson = json_encode($headerDataUrl);
?>

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


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

<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4><span class="text-muted fw-light">Report /</span> Department Wise Report</h4>
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
    <label class="form-label">Department</label>
    <select class="form-select" id="department" name="department">
      <option value="">Select Department</option>
      @foreach ($get_group as $g)
        <option value="{{ $g->unique_id }}" {{ request('department') == $g->unique_id ? 'selected' : '' }}>
          {{ $g->department }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-1">
    <button id="filter_btn" type="button" class="btn btn-success w-100">
      <i class="bi bi-search"></i> Go
    </button>
  </div>
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

    <div class="table-toolbar">
      <div class="d-flex align-items-center gap-2">
        <label class="mb-0">Entries:</label>
        <select id="entriesPerPageTop" class="form-select form-select-sm">
          <option value="10" selected>10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="All">All</option>
        </select>
      </div>
      <div>
        <input id="tableSearch" type="text" class="form-control form-control-sm search-input" placeholder="Search...">
      </div>
    </div>

    <table class="table table-bordered text-center align-middle" id="deptComplaintTable">
      <thead>
        <tr>
          <th style="width:60px">S.no</th>
          <th>Department</th>
          <th>Pending</th>
          <th>Resolved</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $departmentsToShow = $deptFilter 
            ? [ (object)['unique_id'=>$deptFilter,'department'=>\DB::table('department')->where('unique_id',$deptFilter)->value('department') ?? $deptFilter] ]
            : $get_group;

        $rowNo = 1;
        foreach ($departmentsToShow as $deptRow) {
          $q = \DB::table('new_complaints');
          if ($hasIsDelete) $q->where('is_delete',0);
          if ($deptCol) $q->where($deptCol,$deptRow->unique_id);
          if ($dateCol) {
            if ($from) $q->whereDate($dateCol,'>=',$from);
            if ($to) $q->whereDate($dateCol,'<=',$to);
          }
          $pending  = $statusCol ? (clone $q)->where($statusCol,0)->count() : 0;
          $resolved = $statusCol ? (clone $q)->where($statusCol,2)->count() : 0;
          $total    = (clone $q)->count();
          if ($total === 0) continue;
      ?>
        <tr>
          <td>{{ $rowNo++ }}</td>
          <td class="text-start">{{ $deptRow->department ?? '-' }}</td>
          <td>{{ $pending }}</td>
          <td>{{ $resolved }}</td>
          <td>{{ $total }}</td>
        </tr>
      <?php
        }
        if ($rowNo === 1) {
          echo '<tr><td colspan="5" class="text-muted text-center">No data</td></tr>';
        }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="total-label">Total</td>
          <td id="totalPending">0</td>
          <td id="totalResolved">0</td>
          <td id="totalOverall">0</td>
        </tr>
      </tfoot>
    </table>

    <div id="tableFooter" class="table-footer mt-2">
      <nav>
        <ul id="pager" class="pagination pagination-sm"></ul>
      </nav>
    </div>

  </div>
</div>

<script>
document.getElementById('filter_btn').addEventListener('click', function(){
  const params = new URLSearchParams();
  const f = document.getElementById('from_date').value;
  const t = document.getElementById('to_date').value;
  const d = document.getElementById('department').value;
  if(f) params.set('from_date',f);
  if(t) params.set('to_date',t);
  if(d) params.set('department',d);
  window.location="{{ route('departmentcomplaint') }}" + (params.toString() ? ('?'+params.toString()) : '');
});

document.getElementById('print_btn').addEventListener('click', function(){
  const params = new URLSearchParams();
  const f = document.getElementById('from_date').value;
  const t = document.getElementById('to_date').value;
  const d = document.getElementById('department').value;
  if(f) params.set('from_date',f);
  if(t) params.set('to_date',t);
  if(d) params.set('department',d);
  window.open("{{ route('departmentcomplaint.print') }}" + (params.toString() ? ('?'+params.toString()) : ''), '_blank');
});

document.getElementById('excel_btn').addEventListener('click', function(){
  const params = new URLSearchParams();
  const f = document.getElementById('from_date').value;
  const t = document.getElementById('to_date').value;
  const d = document.getElementById('department').value;
  if(f) params.set('from_date',f);
  if(t) params.set('to_date',t);
  if(d) params.set('department',d);
  window.location="{{ route('departmentcomplaint.export') }}" + (params.toString() ? ('?'+params.toString()) : '');
});

// Pagination & totals update
(function(){
  const table = document.getElementById('deptComplaintTable');
  const tbody = table.querySelector('tbody');
  const allRows = Array.from(tbody.querySelectorAll('tr'));
  const perSel = document.getElementById('entriesPerPageTop');
  const pager  = document.getElementById('pager');
  const search = document.getElementById('tableSearch');

  function getPerPage(){ const v=perSel.value; return v==='All'?Number.MAX_SAFE_INTEGER:parseInt(v,10);}
  function getFilteredRows(){ const q=(search.value||'').toLowerCase().trim(); return !q?allRows.slice():allRows.filter(tr=>tr.textContent.toLowerCase().includes(q)); }
  let current = 1;

  function showPage(page){
    const rows = getFilteredRows();
    const perPage = getPerPage();
    const total = rows.length;
    const totalPages = Math.max(1, Math.ceil(total/perPage));
    current = Math.min(Math.max(1,page), totalPages);
    allRows.forEach(r=>r.style.display='none');
    const start=(current-1)*perPage;
    const end=Math.min(start+perPage,total);
    rows.slice(start,end).forEach(r=>r.style.display='');
    updateTotals(rows.slice(start,end));
    renderPager(totalPages,current);
  }

  function updateTotals(rows){
    let p=0,r=0,t=0;
    rows.forEach(tr=>{
      const c=tr.querySelectorAll('td');
      p+=parseInt(c[2].textContent)||0;
      r+=parseInt(c[3].textContent)||0;
      t+=parseInt(c[4].textContent)||0;
    });
    document.getElementById('totalPending').textContent=p;
    document.getElementById('totalResolved').textContent=r;
    document.getElementById('totalOverall').textContent=t;
  }

  function range(s,e){return Array.from({length:e-s+1},(_,i)=>i+s);}
  function pageList(totalPages,page,maxLength){
    if(totalPages<=maxLength) return range(1,totalPages);
    const side=2,wing=maxLength-3,left=Math.max(2,page-side),right=Math.min(totalPages-1,page+side);
    if(left<=3) return [...range(1,wing),0,totalPages];
    if(right>=totalPages-2) return [1,0,...range(totalPages-wing+1,totalPages)];
    return [1,0,...range(left,right),0,totalPages];
  }

  function renderPager(totalPages,page){
    pager.innerHTML=''; if(totalPages<=1){pager.style.display='none';return;} pager.style.display='';
    const mk=(label,disabled,targetPage,aria)=>{ const li=document.createElement('li'); li.className='page-item'+(disabled?' disabled':''); const a=document.createElement('a'); a.className='page-link'; a.href='javascript:void(0)'; a.setAttribute('aria-label',aria||label); a.textContent=label; if(!disabled)a.addEventListener('click',()=>showPage(targetPage)); li.appendChild(a); return li; };
    pager.appendChild(mk('«',page===1,1,'First')); pager.appendChild(mk('‹',page===1,page-1,'Previous'));
    pageList(totalPages,page,7).forEach(n=>{ if(n===0){ const li=document.createElement('li'); li.className='page-item disabled'; li.innerHTML='<span class="page-link">…</span>'; pager.appendChild(li); } else { const li=document.createElement('li'); li.className='page-item'+(n===page?' active':''); li.innerHTML=`<a class="page-link" href="javascript:void(0)">${n}</a>`; li.addEventListener('click',()=>showPage(n)); pager.appendChild(li); } });
    pager.appendChild(mk('›',page===totalPages,page+1,'Next')); pager.appendChild(mk('»',page===totalPages,totalPages,'Last'));
  }

  perSel.addEventListener('change',()=>showPage(1));
  search.addEventListener('input',()=>showPage(1));
  showPage(1);
})();
</script>
@endsection
