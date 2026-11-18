<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Street Light Report</title>
  <style>
    @page { size: A4 landscape; margin: 20mm 12mm 12mm 12mm; }
    html, body { margin:0; padding:0; font-family: Arial,sans-serif; font-size:12px; color:#000; }
    .page { box-sizing:border-box; padding:0; }
    .report-header { text-align:center; margin-bottom:8px; break-inside:avoid; page-break-inside:avoid; }
    .report-header img { display:block; margin:0 auto 6px auto; max-width:100%; height:auto; max-height:120px; }
    .report-title { margin:6px 0; font-weight:700; font-size:14px; }
    .report-sub { margin:0 0 10px 0; font-size:12px; }
    table { width:100%; border-collapse:collapse; margin:0 0 6px 0; page-break-inside:auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    th, td { border:1px solid #000; padding:6px; text-align:center; vertical-align:middle; word-break:break-word; }
    th { background:#C0C0C0; font-weight:bold; font-size:12px; } /* Updated heading color */
    td.text-start { text-align:left; }
    /* Adjust column widths */
    th.address, td.address { width:15%; }
    th.mobile, td.mobile { width:12%; }
    th.status, td.status { width:12%; }
    tr { page-break-inside:avoid; }
    .print-footer { font-size:10px; color:#333; margin-top:6px; }
  </style>
</head>
<body onload="window.print();">
  <div class="page">
    <div class="report-header">
      <img src="{{ asset('public/uploads/header.png') }}" alt="Header">
      <div class="report-title">New Street Light Report</div>
      <div class="report-sub">
        From: {{ $from ?: 'All' }} | To: {{ $to ?: 'All' }}
      </div>
    </div>

    <div class="content">
      <table>
        <thead>
          <tr>
            <th>S.No</th>
            <th>Complaint ID</th>
            <th>Name</th>
            <th class="mobile">Mobile No</th>
            <th class="address">Address</th>
            <th>Zone</th>
            <th>Ward</th>
            <th>Source Name</th>
            <th class="status">Status</th>
          </tr>
        </thead>
        <tbody>
          @php $i = 1; @endphp
          @forelse($rows as $r)
            @php
              $cid = trim((data_get($r,'grievance_prefix','') ?? '') . (data_get($r,'grievance_id','') ?? ''));
            @endphp
            <tr>
              <td>{{ $i++ }}</td>
              <td class="text-start">{{ $cid ?: '-' }}</td>
              <td class="text-start">{{ data_get($r,'name','-') }}</td>
              <td class="mobile">{{ data_get($r,'mobile_no','-') }}</td>
              <td class="text-start address">{{ data_get($r,'address1','-') }}</td>
              <td>{{ data_get($r,'zone_label','-') }}</td>
              <td>{{ data_get($r,'ward_label','-') }}</td>
              <td class="text-start">{{ data_get($r,'source_label','-') }}</td>
              <td class="status">{{ ((int)(data_get($r,'status',0)) === 0) ? 'Pending' : 'Resolved' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted">No data</td>
            </tr>
          @endforelse

          @if($rows->count())
            <tr style="font-weight:bold; background:#f9f9f9;">
              <td colspan="9" class="text-start">Total Complaints: {{ $rows->count() }}</td>
            </tr>
          @endif
        </tbody>
      </table>

      <div class="print-footer">
        {{ url()->current() }}
      </div>
    </div>
  </div>
</body>
</html>
