<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Department Complaint Report</title>
  <style>
    @page {
      size: A4;
      margin: 20mm 12mm 12mm 12mm;
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      font-size: 12px;
      color: #000;
    }

    .page {
      box-sizing: border-box;
      padding: 0;
    }

    .report-header {
      text-align: center;
      margin-bottom: 8px;
      break-inside: avoid;
      -webkit-column-break-inside: avoid;
      page-break-inside: avoid;
    }
    .report-header img {
      display: block;
      margin: 0 auto 6px auto;
      max-width: 100%;
      height: auto;
      max-height: 120px;
    }

    .report-title {
      margin: 6px 0;
      font-weight: 700;
      font-size: 14px;
    }
    .report-sub {
      margin: 0 0 10px 0;
      font-size: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 0 6px 0;
      page-break-inside: auto;
    }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }

    th, td {
      border: 1px solid #000;
      padding: 6px;
      text-align: center;
      vertical-align: middle;
      word-break: break-word;
    }

    th {
      background: #C0C0C0;
      font-weight: bold;
      font-size: 12px;
    }

    td.text-start { text-align: left; }
    td.text-end { text-align: right; }

    tfoot td {
      font-weight: 700;
      background: #f1f3f5;
    }

    tr { page-break-inside: avoid; }

    .content {
      margin-top: 6px;
    }

    .print-footer {
      font-size: 10px;
      color: #333;
      margin-top: 6px;
    }

    @media print {
      .report-header img { max-height: 120px; }
    }
  </style>
</head>
<body onload="window.print();">
  <div class="page">
    <div class="report-header">
      @php
        $imgSrc = $headerDataUrl ?? asset('public/uploads/header.png');
      @endphp

      @if(!empty($imgSrc))
        <img src="{{ $imgSrc }}" alt="Header">
      @endif

      <div class="report-title">Department Complaint Wise Report</div>
      <div class="report-sub">From: {{ $from ?: 'All' }} | To: {{ $to ?: 'All' }}</div>
    </div>

    <div class="content">
      <table>
        <thead>
          <tr>
            <th style="width:60px">S.No</th>
            <th>Department</th>
            <th style="width:80px">Pending</th>
            <th style="width:80px">Resolved</th>
            <th style="width:80px">Total</th>
          </tr>
        </thead>
        <tbody>
          @php 
            $i = 1; 
            $totalPending = 0;
            $totalResolved = 0;
            $grandTotal = 0;
          @endphp

          @foreach($departments as $d)
            @php
              $q = DB::table('new_complaints')
                    ->when($d->unique_id, fn($qq)=>$qq->where('department', $d->unique_id));

              if (!empty($from)) $q->whereDate('created_at', '>=', $from);
              if (!empty($to))   $q->whereDate('created_at', '<=', $to);
              if (!empty($dept)) $q->where('department', $dept);

              $pending = (clone $q)->where('status', 0)->count();
              $resolved = (clone $q)->where('status', 2)->count();
              $total = (clone $q)->count();

              $totalPending += $pending;
              $totalResolved += $resolved;
              $grandTotal += $total;
            @endphp

            @if($total > 0)
              <tr>
                <td>{{ $i++ }}</td>
                <td class="text-start">{{ $d->department }}</td>
                <td>{{ $pending }}</td>
                <td>{{ $resolved }}</td>
                <td>{{ $total }}</td>
              </tr>
            @endif
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2" class="text-center"><strong>Total</strong></td>
            <td><strong>{{ $totalPending }}</strong></td>
            <td><strong>{{ $totalResolved }}</strong></td>
            <td><strong>{{ $grandTotal }}</strong></td>
          </tr>
        </tfoot>
      </table>

      <div class="print-footer">
        {{ url()->current() }}
      </div>
    </div>
  </div>
</body>
</html>
