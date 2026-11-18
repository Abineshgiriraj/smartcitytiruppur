@php
$wardId = request('ward');
$deptId = request('department');
$from   = request('from_date');
$to     = request('to_date');

$dateCol   = null; foreach (['created_at','date','occur_time','complaint_date'] as $c) { if (Schema::hasColumn('new_complaints',$c)) {$dateCol=$c;break;} }
$deptCol   = null; foreach (['department','dept','dept_id','department_id'] as $c) { if (Schema::hasColumn('new_complaints',$c)) {$deptCol=$c;break;} }
$wardCol   = null; foreach (['ward','ward_id','wardid','ward_unique_id'] as $c) { if (Schema::hasColumn('new_complaints',$c)) {$wardCol=$c;break;} }
$statusCol = null; foreach (['status','complaint_status'] as $c) { if (Schema::hasColumn('new_complaints',$c)) {$statusCol=$c;break;} }
$hasIsDelete = Schema::hasColumn('new_complaints','is_delete');

$nameCols    = ['name','applicant_name','citizen_name','customer_name'];
$mobileCols  = ['mobile','mobile_no','mobile_number','phone','contact','contact_no','contact_number'];
$grievCols   = ['grievance_id','complaint_id','complaint_no','ticket_no','ticket_number'];
$descCols    = ['description','remarks','complaint_description','details','issue','problem'];

$pick = function($row,$cands,$fallback='-'){
  foreach($cands as $c){ if($c && isset($row->$c) && $row->$c!==''){ return is_string($row->$c)?trim($row->$c):$row->$c; } }
  return $fallback;
};

$q = DB::table('new_complaints');
if ($hasIsDelete) $q->where('is_delete',0);
if ($wardId && $wardCol) $q->where($wardCol,$wardId);
if ($deptId && $deptCol) $q->where($deptCol,$deptId);
if ($dateCol){
  if ($from) $q->whereDate($dateCol,'>=',$from);
  if ($to)   $q->whereDate($dateCol,'<=',$to);
}
$rows = $q->orderByDesc($dateCol ?? 'created_at')->get();
@endphp

<style>
  .nowrap { white-space: nowrap; }
  #detailPagination .page-link { cursor: pointer; }
</style>

<div class="table-responsive">
  <table id="detailTable" class="table table-sm table-bordered align-middle">
    <thead class="table-light text-center">
      <tr>
        <th style="width:60px">S.no</th>
        <th>Name</th>
        <th>Mobile</th>
        <th class="nowrap">Grievance ID</th>
        <th>Department</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $r)
        @php
          $nm   = $pick($r,$nameCols);
          $mob  = $pick($r,$mobileCols);
          $gid  = $pick($r,$grievCols);
          $desc = $pick($r,$descCols);

          $deptRaw = $deptCol ? ($r->$deptCol ?? '-') : '-';
          $deptTxt = DB::table('department')->where('unique_id',$deptRaw)->value('department') ?? ($deptRaw ?: '-');

          $statusRaw = $statusCol ? ($r->$statusCol ?? null) : null;
          $statusLbl = match((string)$statusRaw) {
            '0' => 'Pending',
            '2' => 'Completed',
            default => (is_null($statusRaw) ? '-' : (string)$statusRaw),
          };
          $statusClass = $statusLbl === 'Pending' ? 'text-danger' : ($statusLbl === 'Completed' ? 'text-success' : 'text-muted');
        @endphp
        <tr class="data-row">
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-start">{{ $nm }}</td>
          <td class="text-start nowrap">{{ $mob }}</td>
          <td class="text-start nowrap">{{ $gid }}</td>
          <td class="text-start">{{ $deptTxt }}</td>
          <td class="text-start">{{ $desc }}</td>
          <td class="text-start"><span class="{{ $statusClass }} fw-semibold">{{ $statusLbl }}</span></td>
        </tr>
      @empty
        <tr class="data-row">
          <td colspan="7" class="text-center text-muted">No records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div id="detailPagination" class="d-flex justify-content-between align-items-center mt-2">
  <div class="small text-muted" id="pageInfo"></div>
  <nav><ul class="pagination pagination-sm mb-0" id="pager"></ul></nav>
</div>

<script>
(function(){
  const table = document.getElementById('detailTable');
  if(!table) return;
  const rows = Array.from(table.querySelectorAll('tbody tr.data-row'));
  const perPage = 10; // ? modal pagination 10/pg
  let current = 1;
  const pager = document.getElementById('pager'), info = document.getElementById('pageInfo');

  function build(label,page,disabled=false,active=false){
    const li=document.createElement('li');
    li.className='page-item'+(disabled?' disabled':'')+(active?' active':'');
    const a=document.createElement('a'); a.className='page-link'; a.href='#'; a.textContent=label;
    a.addEventListener('click',e=>{e.preventDefault(); if(disabled||active) return; current=page; render();});
    li.appendChild(a); return li;
  }
  function render(){
    const total=rows.length, pages=Math.max(1,Math.ceil(total/perPage));
    if (current>pages) current=pages;
    const start=(current-1)*perPage, end=start+perPage;
    rows.forEach((tr,i)=>{ tr.style.display=(i>=start&&i<end)?'':'none'; });
    pager.innerHTML='';
    pager.appendChild(build('<<',1,current===1));
    pager.appendChild(build('<',Math.max(1,current-1),current===1));
    const win=5; let from=Math.max(1,current-Math.floor(win/2)); let to=Math.min(pages,from+win-1); from=Math.max(1,Math.min(from,pages-win+1));
    for(let p=from;p<=to;p++){ pager.appendChild(build(String(p),p,false,p===current)); }
    pager.appendChild(build('>',Math.min(pages,current+1),current===pages));
    pager.appendChild(build('>>',pages,current===pages));
    const s= total ? start+1 : 0, e=Math.min(end,total); info.textContent=`Showing ${s}�${e} of ${total}`;
  }
  render();
})();
</script>
