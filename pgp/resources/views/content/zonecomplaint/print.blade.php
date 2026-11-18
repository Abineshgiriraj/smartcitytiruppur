<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Zone Complaint Report</title>
  <style>
    @page { size: A4; margin: 20mm 12mm 12mm 12mm; }
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
    tr { page-break-inside:avoid; }
    .print-footer { font-size:10px; color:#333; margin-top:6px; }
  </style>
</head>
<body onload="window.print();">
  <div class="page">
    <div class="report-header">
      <img src="{{ asset('public/uploads/header.png') }}" alt="Header">
      <div class="report-title">Zone Complaint Report</div>
      <div class="report-sub">
        From: {{ $from ?: 'All' }} | To: {{ $to ?: 'All' }} |
        Zone: {{ $zoneFilter ? get_zone_name($zoneFilter) : 'All' }}
      </div>
    </div>

    <div class="content">
      <table>
        <thead>
          <tr>
            <th>Zone</th>
            <th>Complaint Source</th>
            <th>Pending</th>
            <th>Resolved</th>
            <th>Total</th>
          </tr>
        </thead>
    <tbody>
  @forelse($report as $zoneBlock)
    @php $rows = $zoneBlock['rows'] ?? []; $printedZoneCell = false; @endphp

    @foreach($rows as $r)
      <tr>
        @if(!$printedZoneCell)
          <td rowspan="{{ max(1, count($rows)) }}">{{ $zoneBlock['zone_name'] }}</td>
          @php $printedZoneCell = true; @endphp
        @endif
        <td class="text-start">{{ $r['source_name'] }}</td>
        <td>{{ $r['pending'] }}</td>
        <td>{{ $r['resolved'] }}</td>
        <td>{{ $r['total'] }}</td>
      </tr>
    @endforeach

    <tr style="font-weight:bold; background:#f9f9f9;">
      <td></td>
      <td class="text-end">Total</td>
      <td>{{ $zoneBlock['totals']['pending'] }}</td>
      <td>{{ $zoneBlock['totals']['resolved'] }}</td>
      <td>{{ $zoneBlock['totals']['total'] }}</td>
    </tr>
  @empty
    <tr>
      <td colspan="5" class="text-center text-muted">No data</td>
    </tr>
  @endforelse
</tbody>
      </table>

      <div class="print-footer">
        {{ url()->current() }}
      </div>
    </div>
  </div>
</body>
</html>
