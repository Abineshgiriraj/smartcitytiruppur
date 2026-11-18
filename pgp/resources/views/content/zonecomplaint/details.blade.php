@php
// NOTE: Don't "use" namespaces inside Blade @php; just call facades via global aliases.

$zoneId   = request('zone');
$sourceId = request('source');
$from     = request('from_date');
$to       = request('to_date');

/** Discover column names flexibly so it works with your schema */
$dateCol = null;
foreach (['created_at','date','occur_time','complaint_date'] as $c) {
  if (\Schema::hasColumn('new_complaints', $c)) { $dateCol = $c; break; }
}
$zoneCol = null;
foreach (['zone','zone_id'] as $c) {
  if (\Schema::hasColumn('new_complaints', $c)) { $zoneCol = $c; break; }
}
$wardCol = null;
foreach (['ward','ward_id','ward_name'] as $c) {
  if (\Schema::hasColumn('new_complaints', $c)) { $wardCol = $c; break; }
}
$sourceCol = null;
foreach (['source_name','source','source_id'] as $c) {
  if (\Schema::hasColumn('new_complaints', $c)) { $sourceCol = $c; break; }
}
$statusCol = null;
foreach (['status','complaint_status'] as $c) {
  if (\Schema::hasColumn('new_complaints', $c)) { $statusCol = $c; break; }
}
$hasIsDelete = \Schema::hasColumn('new_complaints','is_delete');

/** Build query respecting filters */
$q = \DB::table('new_complaints');
if ($hasIsDelete)              { $q->where('is_delete', 0); }
if ($zoneId && $zoneCol)       { $q->where($zoneCol, $zoneId); }
if ($sourceId && $sourceCol)   { $q->where($sourceCol, $sourceId); }
if ($dateCol) {
  if ($from) { $q->whereDate($dateCol, '>=', $from); }
  if ($to)   { $q->whereDate($dateCol, '<=', $to); }
}
$rows = $q->get();

/** ------- Lookups so we display NAMES instead of IDs ------- */
$makeMap = function(array $tables, array $nameCandidates) {
  foreach ($tables as $t) {
    if (!\Schema::hasTable($t)) continue;
    $nameCol = null;
    foreach ($nameCandidates as $c) {
      if (\Schema::hasColumn($t, $c)) { $nameCol = $c; break; }
    }
    if (!$nameCol) continue;
    $qq = \DB::table($t);
    if (\Schema::hasColumn($t,'is_delete')) $qq->where('is_delete', 0);
    if (\Schema::hasColumn($t,'is_active')) $qq->where('is_active', 1);
    if (!\Schema::hasColumn($t,'unique_id')) continue;
    return $qq->pluck($nameCol, 'unique_id')->toArray();
  }
  return [];
};

$zoneMap    = $makeMap(['zone'], ['zone','zone_name','name']);
$wardMap    = $makeMap(['ward','ward_creation','wards','ward_master'], ['ward_name','ward','name']);
$deptMap    = $makeMap(['department','department_creation','departments','department_master'], ['department_name','department','name']);
$deptCatMap = $makeMap(['department_category','dept_category','department_categories'], ['department_category','category','name']);
$sourceMap  = $makeMap(['source_name','sources'], ['source_name','name','source']);
$locationMap = $makeMap(
  ['location','locations','location_master','location_creation','places','place_master','street','streets','street_master','area','areas','area_master','locality','localities','locality_master','addresses','address_book'],
  ['location','location_name','name','place','place_name','street','street_name','area','area_name','address','address_line','address1','address_1','full_address','landmark']
);

$mapIfId = function($val, $map) {
  if ($val === null || $val === '' || $val === '-') return '-';
  return $map[$val] ?? $val;
};

/** Helpers to pick first-available field */
$pick = function($row, $cands, $fallback='-') {
  foreach ($cands as $c) {
    if ($c && isset($row->$c) && $row->$c !== '' && $row->$c !== null) {
      return is_string($row->$c) ? trim($row->$c) : $row->$c;
    }
  }
  return $fallback;
};

/** Column candidates for display (raw from new_complaints) */
$nameCols       = ['name','applicant_name','citizen_name','customer_name'];
$mobileCols     = ['mobile','mobile_no','mobile_number','phone','contact','contact_no','contact_number'];
$grievanceCols  = ['grievance_id','complaint_id','complaint_no','ticket_no','ticket_number'];
$zoneNameCols   = ['zone_name','zone',$zoneCol];
$wardNameCols   = ['ward_name','ward',$wardCol];
$locationCols   = ['location','address','place','area','street'];
$deptCols       = ['department_name','department','dept','dept_id'];
$deptCatCols    = ['department_category','dept_category','category','category_name'];
$sourceNameCols = ['source_name','source',$sourceCol];
@endphp

<style>
  .nowrap { white-space: nowrap; }
  .zone-ward { line-height: 1.2; }
  /* pagination */
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
        <th>Zone/<br>Ward</th>
        <th>Location</th>
        <th>Department</th>
        <th class="nowrap">Source</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $r)
        @php
          $name      = $pick($r, $nameCols);
          $mobile    = $pick($r, $mobileCols);
          $grievance = $pick($r, $grievanceCols);

          $zoneRaw   = $pick($r, $zoneNameCols);
          $wardRaw   = $pick($r, $wardNameCols);
          $zoneTxt   = $mapIfId($zoneRaw, $zoneMap);
          $wardTxt   = $mapIfId($wardRaw, $wardMap);

          $zwParts = [];
          if ($zoneTxt !== '-' && $zoneTxt !== '') $zwParts[] = $zoneTxt;
          if ($wardTxt !== '-' && $wardTxt !== '') $zwParts[] = $wardTxt;
          $zoneWardHtml = count($zwParts) ? implode('<br>', $zwParts) : '-';

          $locationRaw = $pick($r, $locationCols);
          $location    = $mapIfId($locationRaw, $locationMap);

          $deptRaw   = $pick($r, $deptCols);
          $dept      = $mapIfId($deptRaw, $deptMap);

          $deptCatRaw = $pick($r, $deptCatCols);
          $deptCat    = $mapIfId($deptCatRaw, $deptCatMap);

          $sourceRaw = $pick($r, $sourceNameCols);
          $source    = $mapIfId($sourceRaw, $sourceMap);

          $statusRaw = $statusCol ? ($r->$statusCol ?? null) : null;
          $statusLbl = match((string)$statusRaw) {
            '0' => 'Pending',
            '2' => 'Completed',
            default => (is_null($statusRaw) ? '-' : (is_string($statusRaw) ? ucfirst($statusRaw) : (string)$statusRaw)),
          };
          $statusClass = $statusLbl === 'Pending' ? 'text-danger' : ($statusLbl === 'Completed' ? 'text-success' : 'text-muted');
        @endphp
        <tr class="data-row">
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-start">{{ $name }}</td>
          <td class="text-start nowrap">{{ $mobile }}</td>
          <td class="text-start nowrap">{{ $grievance }}</td>
          <td class="text-start zone-ward">{!! $zoneWardHtml !!}</td>
          <td class="text-start">{{ $location }}</td>
          <td class="text-start">
            <div>{{ $dept ?: '-' }}</div>
            @if($deptCat && $deptCat !== '-')
              <div class="text-muted small">({{ $deptCat }})</div>
            @endif
          </td>
          <td class="text-start nowrap">{{ $source }}</td>
          <td class="text-start"><span class="{{ $statusClass }} fw-semibold">{{ $statusLbl }}</span></td>
        </tr>
      @empty
        <tr class="data-row">
          <td colspan="9" class="text-center text-muted">No records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div id="detailPagination" class="d-flex justify-content-between align-items-center mt-2">
  <div class="small text-muted" id="pageInfo"></div>
  <nav>
    <ul class="pagination pagination-sm mb-0" id="pager"></ul>
  </nav>
</div>

<script>
(function(){
  const table = document.getElementById('detailTable');
  if(!table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr.data-row'));
  const perPage = 10; // default 10 entries
  let current = 1;

  const pager = document.getElementById('pager');
  const info  = document.getElementById('pageInfo');

  function buildItem(label, page, disabled=false, active=false) {
    const li = document.createElement('li');
    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href = '#';
    a.textContent = label;
    a.addEventListener('click', function(e){
      e.preventDefault();
      if (disabled || active) return;
      current = page;
      render();
    });
    li.appendChild(a);
    return li;
  }

  function render(){
    const total = rows.length;
    const pages = Math.max(1, Math.ceil(total / perPage));
    if (current > pages) current = pages;

    const start = (current - 1) * perPage;
    const end   = start + perPage;

    rows.forEach((tr, i) => { tr.style.display = (i >= start && i < end) ? '' : 'none'; });

    // pager
    pager.innerHTML = '';
    pager.appendChild(buildItem('«', 1, current === 1));
    pager.appendChild(buildItem('‹', Math.max(1, current - 1), current === 1));

    const windowSize = 5;
    let from = Math.max(1, current - Math.floor(windowSize / 2));
    let to   = Math.min(pages, from + windowSize - 1);
    from = Math.max(1, Math.min(from, pages - windowSize + 1));

    for (let p = from; p <= to; p++) {
      pager.appendChild(buildItem(String(p), p, false, p === current));
    }

    pager.appendChild(buildItem('›', Math.min(pages, current + 1), current === pages));
    pager.appendChild(buildItem('»', pages, current === pages));

    const shownStart = total ? start + 1 : 0;
    const shownEnd   = Math.min(end, total);
    info.textContent = `Showing ${shownStart}–${shownEnd} of ${total}`;
  }

  render();
})();
</script>
