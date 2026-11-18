@extends('layouts/contentNavbarLayout')

@section('title', 'Street Light Report')

<style>
  .btn-outline-primary{
    --bs-btn-color:#0d6efd;
    --bs-btn-border-color:#0d6efd;
    --bs-btn-hover-bg:#0d6efd;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-border-color:#0d6efd;
    --bs-btn-active-bg:#0d6efd;
    --bs-btn-active-border-color:#0d6efd;
  }
  .table > :not(caption) > * > *{ background-color:#fff !important; color:#212529; }
  #slTable thead th{ background:#f1f3f5 !important; color:#212529; }
  @media print { body *{visibility:hidden} #printable, #printable *{visibility:visible} #printable{position:absolute; left:0; top:0; width:100%} }
  .table-toolbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
  .table-toolbar .search-input{ max-width:260px; }
  .table-footer{ display:flex; justify-content:flex-end; align-items:center; gap:.75rem; }
  .table-footer .pagination{ margin:0; }
</style>

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Report /</span> Street Light Report</h4>
  </div>
</div>

<div class="row g-2 align-items-end mb-3">
  <div class="col-md-2">
    <label class="form-label">From Date</label>
    <input type="date" id="from_date" class="form-control" value="{{ $from }}">
  </div>
  <div class="col-md-2">
    <label class="form-label">To Date</label>
    <input type="date" id="to_date" class="form-control" value="{{ $to }}">
  </div>
  <div class="col-md-1">
    <button id="go_btn" type="button" class="btn btn-success w-100">
      <i class="bi bi-search"></i> Go
    </button>
  </div>
  <div class="col-md-7 d-flex justify-content-end gap-2">
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

    <table class="table table-bordered text-center align-middle" id="slTable">
      <thead>
        <tr>
          <th style="width:60px">S.No</th>
          <th>Complaint ID</th>
          <th>Name</th>
          <th>Mobile No</th>
          <th>Address</th>
          <th>Zone</th>
          <th>Ward</th>
          <th>Source Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @forelse($rows as $r)
          @php
            $cid = trim(($r->grievance_prefix ?? '').($r->grievance_id ?? ''));
          @endphp
          <tr>
            <td>{{ $i++ }}</td>
            <td class="text-start">{{ $cid ?: '-' }}</td>
            <td class="text-start">{{ $r->name ?: '-' }}</td>
            <td>{{ $r->mobile_no ?: '-' }}</td>
            <td class="text-start">{{ $r->address1 ?: '-' }}</td>
            <td>{{ $r->zone_label ?: '-' }}</td>
            <td>{{ $r->ward_label ?: '-' }}</td>
            <td class="text-start">{{ $r->source_label ?: '-' }}</td>
            <td>{{ ((int)($r->status ?? 0) === 0) ? 'Pending' : 'Resolved' }}</td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-muted text-center">No data</td></tr>
        @endforelse
      </tbody>
    </table>

    <div id="tableFooter" class="table-footer mt-2">
      <nav><ul id="pager" class="pagination pagination-sm"></ul></nav>
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
// GO
document.getElementById('go_btn').addEventListener('click', function(){
  const p = new URLSearchParams();
  const f = document.getElementById('from_date').value;
  const t = document.getElementById('to_date').value;
  if (f) p.set('from_date', f);
  if (t) p.set('to_date', t);
  window.location = "{{ route('streetlightreport') }}" + (p.toString() ? ('?'+p.toString()) : '');
});


// PRINT using Laravel route
document.getElementById('print_btn').addEventListener('click', function(){
  const from = document.getElementById('from_date').value;
  const to   = document.getElementById('to_date').value;
  const url  = "{{ route('streetlightreport.print') }}?from_date=" + encodeURIComponent(from) + "&to_date=" + encodeURIComponent(to);
  window.open(url, '_blank');
});


// EXCEL
document.getElementById('excel_btn').addEventListener('click', function(){
  const p = new URLSearchParams();
  const f = document.getElementById('from_date').value;
  const t = document.getElementById('to_date').value;
  if (f) p.set('from_date', f);
  if (t) p.set('to_date', t);
  window.location = "{{ route('streetlightreport.export') }}" + (p.toString() ? ('?'+p.toString()) : '');
});

// Client-side pagination + search
(function(){
  const table = document.getElementById('slTable');
  const tbody = table.querySelector('tbody');
  const perSel = document.getElementById('entriesPerPageTop');
  const pager  = document.getElementById('pager');
  const search = document.getElementById('tableSearch');
  const allRows = Array.from(tbody.querySelectorAll('tr'));

  function getPerPage(){ const v = perSel.value; return v==='All'? Number.MAX_SAFE_INTEGER : parseInt(v,10); }
  function getFiltered(){ const q=(search.value||'').toLowerCase(); if(!q) return allRows.slice(); return allRows.filter(tr=>tr.textContent.toLowerCase().includes(q)); }

  let cur=1;
  function show(page){
    const rows=getFiltered(); const per=getPerPage(); const total=rows.length;
    const pages=Math.max(1, Math.ceil(total/Math.min(per,total)));
    cur=Math.min(Math.max(1,page), pages);
    allRows.forEach(r=>r.style.display='none');
    const start=(cur-1)*per, end=Math.min(start+per,total);
    rows.slice(start,end).forEach(r=>r.style.display='');
    renderPager(pages,cur);
  }
  function renderPager(pages,page){
    pager.innerHTML=''; if(pages<=1||perSel.value==='All'){pager.style.display='none';return;} pager.style.display='';
    function li(lbl,dis,tp){ const li=document.createElement('li'); li.className='page-item'+(dis?' disabled':''); const a=document.createElement('a'); a.className='page-link'; a.href='javascript:void(0)'; a.textContent=lbl; if(!dis) a.onclick=()=>show(tp); li.appendChild(a); return li; }
    pager.appendChild(li('«', page===1, 1));
    pager.appendChild(li('‹', page===1, page-1));
    for(let i=1;i<=pages;i++){ const liEl=document.createElement('li'); liEl.className='page-item'+(i===page?' active':''); liEl.innerHTML=`<a class="page-link" href="javascript:void(0)">${i}</a>`; liEl.onclick=()=>show(i); pager.appendChild(liEl); }
    pager.appendChild(li('›', page===pages, page+1));
    pager.appendChild(li('»', page===pages, pages));
  }
  perSel.addEventListener('change',()=>show(1));
  search.addEventListener('input',()=>show(1));
  show(1);
})();
</script>
@endsection
