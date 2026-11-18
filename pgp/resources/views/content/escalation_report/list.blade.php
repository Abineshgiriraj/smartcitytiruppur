@extends('layouts/contentNavbarLayout')

@section('title', 'Escalation Report')

<style>
  .btn-outline-primary{
    --bs-btn-color:#0d6efd;--bs-btn-border-color:#0d6efd;
    --bs-btn-hover-bg:#0d6efd;--bs-btn-hover-color:#fff;--bs-btn-hover-border-color:#0d6efd;
    --bs-btn-active-bg:#0d6efd;--bs-btn-active-border-color:#0d6efd;
  }
  .table > :not(caption) > * > *{ background-color:#fff !important; color:#212529; }
  #slTable thead th{ background:#f1f3f5 !important; color:#212529; text-align:center !important; }
  #slTable tbody td.text-left{ text-align:left !important; }
  #slTable tbody td.text-center-mobile{ text-align:center !important; }
  @media print { body *{visibility:hidden} #printable, #printable *{visibility:visible} #printable{position:absolute; left:0; top:0; width:100%} }
  .table-toolbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; gap:1rem; }
  .table-toolbar .search-input{ max-width:360px; }
  .table-footer{ display:flex; justify-content:flex-end; align-items:center; gap:.75rem; }
  .table-footer .pagination{ margin:0; }
  td, th { vertical-align: middle !important; }
  .wrap-cell { white-space: normal; word-break:break-word; }
  tfoot tr td { font-weight: bold; }
</style>


@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Report /</span> Escalation Report</h4>
  </div>
</div>

<div class="row g-2 align-items-end mb-3">
  <div class="col-md-8"></div>
  <div class="col-md-4 d-flex justify-content-end gap-2">
    <form id="print_form" method="GET" action="{{ route('escalation_report.print') }}" target="_blank">
      <button id="print_btn" type="submit" class="btn btn-outline-primary">
        <i class="bi bi-printer"></i> Print
      </button>
    </form>
    <button id="excel_btn" type="button" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel"></i> Excel
    </button>
  </div>
</div>

<div id="printable" class="card">
  <div class="card-body">

    <div class="table-toolbar">
      <div class="d-flex align-items-center gap-2">
      </div>
    </div>

    <table class="table table-bordered align-middle" id="slTable">
      <thead>
        <tr>
          <th style="width:60px">S.No</th>
          <th style="min-width:160px">Staff Name</th>
          <th style="min-width:120px">Mobile No</th>
          <th style="min-width:100px">Pending Count</th>
          <th style="min-width:100px">Completed Count</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="text-end">Total</td>
          <td id="totalPending" class="text-center">0</td>
          <td id="totalCompleted" class="text-center">0</td>
        </tr>
      </tfoot>
    </table>

    <div id="tableFooter" class="table-footer mt-2">
      <nav><ul id="pager" class="pagination pagination-sm"></ul></nav>
    </div>
  </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
 $(document).ready(function () {
        var table = $('#slTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("escalation_report") }}',
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
                { data: 'staff_name', name: 'staff_name', className: "text-left" },
                { data: 'mobile_no', name: 'mobile_no', className: "text-center-mobile" },
                { data: 'pending_count', name: 'pending_count', className: "text-center" },
                { data: 'completed_count', name: 'completed_count', className: "text-center" }
            ],
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[0, 'asc']],
            drawCallback: function(settings) {
                // calculate totals
                let api = this.api();
                let totalPending = api.column(3, {page:'current'}).data().reduce((a,b)=>a+parseInt(b||0),0);
                let totalCompleted = api.column(4, {page:'current'}).data().reduce((a,b)=>a+parseInt(b||0),0);
                $('#totalPending').html(totalPending);
                $('#totalCompleted').html(totalCompleted);
            }
        });
    });

// EXCEL
document.getElementById('excel_btn').addEventListener('click', function(){
  window.location = "{{ route('escalation_report.export') }}";
});
</script>
@endsection
