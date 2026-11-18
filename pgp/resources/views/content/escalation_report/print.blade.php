<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escalation Report</title>
  <style>
    @page { size: A4 landscape; margin: 12mm; }
    html, body { margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#000; }
    .page { box-sizing:border-box; padding:0; }
    .report-header { text-align:center; margin-bottom:12px; }
    .report-header img { max-height:80px; display:block; margin:0 auto 8px; }
    .report-title { font-weight:700; font-size:15px; margin-bottom:6px; }
    .report-sub { margin-bottom:8px; font-size:12px; color:#333; }
    table { width:100%; border-collapse:collapse; margin:0 0 6px 0; }
    thead { display: table-header-group; }
    th, td { border:1px solid #000; padding:6px; vertical-align:middle; word-break:break-word; }
    th { background:#C0C0C0; font-weight:700; text-align:center; } /* Updated heading color */
    td.text-start { text-align:left; padding-left:6px; }
    td.text-center { text-align:center; }
    td.wrap-cell { white-space: normal; word-break:break-word; text-align:left; padding-left:6px; }
    .total-row td { font-weight:700; text-align:right; }
    .print-footer { font-size:10px; color:#333; margin-top:8px; }
  </style>
</head>
<body onload="window.print();">
  <div class="page">
    <div class="report-header">
      <img src="{{ asset('public/uploads/header.png') }}" alt="Header">
      <div class="report-title">Escalation Report</div>
      <div class="report-sub">Generated: {{ now()->format('d-M-Y H:i') }}</div>
    </div>

    <div class="content">
      <table>
        <thead>
          <tr>
            <th style="width:50px">S.No</th>
            <th style="min-width:180px">Staff Name</th>
            <th style="min-width:120px">Mobile No</th>
            <th style="min-width:100px">Pending Count</th>
            <th style="min-width:100px">Completed Count</th>
          </tr>
        </thead>
        <tbody>
          @php
            $i = 1;
            $total_pending = 0;
            $total_completed = 0;
          @endphp
          @forelse($rows as $r)
            @php
              $total_pending += data_get($r,'pending_count',0);
              $total_completed += data_get($r,'completed_count',0);
            @endphp
            <tr>
              <td class="text-center">{{ $i++ }}</td>
              <td class="wrap-cell">{{ data_get($r,'staff_name','-') }}</td>
              <td class="text-center">{{ data_get($r,'mobile_no','-') }}</td>
              <td class="text-center">{{ data_get($r,'pending_count', 0) }}</td>
              <td class="text-center">{{ data_get($r,'completed_count', 0) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">No data</td></tr>
          @endforelse
          @if($rows->count() > 0)
            <tr class="total-row">
              <td colspan="3" class="text-end">Total</td>
              <td class="text-center">{{ $total_pending }}</td>
              <td class="text-center">{{ $total_completed }}</td>
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
