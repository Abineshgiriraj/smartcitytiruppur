@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<?php $get_zone_name = get_zone(); ?>
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Grievance Module /</span> New Grievance</h4>
        <a href="{{ route('new_complaints_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" id="grievanceTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">Completed</button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="grievanceTabsContent">

    <!-- Pending Tab -->
    <div class="tab-pane fade show active" id="pending" role="tabpanel">

        <!-- Filters for Pending -->
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Zone</label>
                 <select class="form-select" id="zone" name="zone">
      <option value="">Select Zone</option>
      @foreach ($get_zone_name as $get_zone)
        <option value="{{ $get_zone->unique_id }}" {{ request('zone') == $get_zone->unique_id ? 'selected' : '' }}>
          {{ $get_zone->zone }}
        </option>
      @endforeach
    </select>
            </div>
            <div class="col-md-1">
                <button id="filter_btn_pending" type="button" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Go
                </button>
            </div>
            <!--<div class="col-md-5 d-flex justify-content-end gap-2">-->
            <!--    <button id="print_btn_pending" class="btn btn-outline-primary">-->
            <!--        <i class="bi bi-printer"></i> Print-->
            <!--    </button>-->
            <!--    <button id="excel_btn_pending" class="btn btn-outline-success">-->
            <!--        <i class="bi bi-file-earmark-excel"></i> Excel-->
            <!--    </button>-->
            <!--</div>-->
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="dt-responsive table border-top" id="new_complaints_pending">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Grievance ID</th>
                                <th>Name</th>
                                <th>Mobile No</th>
                                <th>Zone</th>
                                <th>Location</th>
                                <th>Source</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <!-- More rows -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tab -->
<div class="tab-pane fade" id="completed" role="tabpanel">

    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-2">
            <label class="form-label">Month</label>
            <input type="month" id="month_completed" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Zone</label>
           <select class="form-select" id="zone_com" name="zone_com">
      <option value="">Select Zone</option>
      @foreach ($get_zone_name as $get_zone)
        <option value="{{ $get_zone->unique_id }}" {{ request('zone') == $get_zone->unique_id ? 'selected' : '' }}>
          {{ $get_zone->zone }}
        </option>
      @endforeach
    </select>
        </div>
        <div class="col-md-1">
            <button id="filter_btn_completed" type="button" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Go
            </button>
        </div>
        <!--<div class="col-md-5 d-flex justify-content-end gap-2">-->
        <!--    <button id="print_btn_completed" class="btn btn-outline-primary">-->
        <!--        <i class="bi bi-printer"></i> Print-->
        <!--    </button>-->
        <!--    <button id="excel_btn_completed" class="btn btn-outline-success">-->
        <!--        <i class="bi bi-file-earmark-excel"></i> Excel-->
        <!--    </button>-->
        <!--</div>-->
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="card-datatable table-responsive">
                <table class="dt-responsive table border-top w-100" id="new_complaints_completed">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Grievance ID</th>
                            <th>Name</th>
                            <th>Mobile No</th>
                            <th>Zone</th>
                            <th>Location</th>
                            <th>Source</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filled dynamically by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


</div>
<!-- jQuery (must be first) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome (only include once) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- DataTables core CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Excel export dependency -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
function toggleComplaintRow(element) {
    const currentRow = element.closest("tr");
    const detailsRow = currentRow.nextElementSibling;
    if (detailsRow && detailsRow.classList.contains("complaint-details-row")) {
        detailsRow.style.display = (detailsRow.style.display === "none" || detailsRow.style.display === "") 
            ? "table-row" 
            : "none";
    }
}
$(document).ready(function () {

    // ================= Pending tab table =================
    let tablePending = $('#new_complaints_pending').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("new_complaints") }}',
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.zone = $('#zone').val();
            }
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
            { data: 'grievance_col', name: 'grievance_col' },
            { data: 'name_col', name: 'name_col' },
            { data: 'mobile_no', name: 'mobile_no' },
            { data: 'zone_col', name: 'zone_col' },
            { data: 'location_name', name: 'location_name' },
            { data: 'source_name', name: 'source_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Print',
                className: 'd-none',
                title: 'Pending Complaints'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'd-none',
                title: 'Pending Complaints'
            }
        ]
    });

    // ✅ Reload pending on filter
    $('#filter_btn_pending').click(function () {
        tablePending.ajax.reload();
    });

    // ================= Completed tab table =================
    let tableCompleted = $('#new_complaints_completed').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("new_complaints_com") }}',
            data: function (d) {
                d.month = $('#month_completed').val(); // send selected month
                d.zone = $('#zone_com').val();             // send selected zone
            }
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
            { data: 'grievance_col_1', name: 'grievance_col_1' },
            { data: 'name_col_1', name: 'name_col_1' },
            { data: 'mobile_no', name: 'mobile_no' },
            { data: 'zone_col_1', name: 'zone_col_1' },
            { data: 'location_name', name: 'location_name' },
            { data: 'source_name', name: 'source_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" }
        ]
    });

    // ✅ Reload completed on filter
    $('#filter_btn_completed').click(function () {
        tableCompleted.ajax.reload();
    });

    // ✅ Fix for hidden tab column sizing
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
    });

});

    
    



        // function confirmDelete(id) {
        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "This will delete the category.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: '{{ route("zone.delete", ":id") }}'.replace(':id', id),
        //                 type: 'POST',
        //                 data: {
        //                     _method: 'DELETE',
        //                     _token: '{{ csrf_token() }}'
        //                 },
        //                 success: function (response) {
        //                     $('#zone_table').DataTable().ajax.reload();
        //                     Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
        //                 },
        //                 error: function () {
        //                     Swal.fire('Error!', 'Something went wrong.', 'error');
        //                 }
        //             });
        //         }
        //     });
        // }


</script>

@endsection