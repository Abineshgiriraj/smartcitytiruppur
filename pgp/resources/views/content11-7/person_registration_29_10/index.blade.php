@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')
<style>
/* btn style */
.btn-right {
    float: right;
}

.btn-medium-custom {
    padding: 6px 12px;
    font-size: 14px;
}

.btn-small {
    /* width: auto; */
    white-space: nowrap;
    padding: 0;
    /* Remove padding */
    margin: 0;
    display: inline-flex;
    /* Align content properly */
    align-items: center;
    width: 55px;
    /* Center icons and text vertically */
    /* Remove margin */
}

.lab_add {
    white-space: nowrap;
    font-size: 10px;
}
table.dataTable th.dt-type-numeric, table.dataTable th.dt-type-date, table.dataTable td.dt-type-numeric, table.dataTable td.dt-type-date {
    text-align: left !important;
}
</style>

@php
$get_designations = get_designation();
@endphp

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">User Firms /</span> Person Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location='{{ route('person_registration_add') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>


</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">

            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control" value="">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control" value="">
            </div>



            <div class="col-2">
                <label class="form-label">Designation</label>

                <select class="form-select" id="designation" name="designation">
                    <option selected>Select Designation</option>
                    @foreach ($get_designations as $designation)
                    <option value="{{$designation->unique_id}}">{{$designation->designation}}</option>

                    @endforeach


                </select>
            </div>
            <div class="col-2"><label for="example-select" class="form-label">Status Filter</label>
                <select name="status" id="status" class="select2 form-control" required>
                    <option value="">Status Filter</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Reject</option>
                </select>
            </div>
            <div class="col-2 mt-4">
                <button id="filterBtn" class="btn btn-primary">GO</button>

            </div>
        </div>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card mt-3">

    <div class="card-body">

        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive" id="person_registration">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Nationality</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<!--/ Responsive Datatable -->
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

<script type="text/javascript" class="init">
$(document).ready(function() {

    var table = $('#person_registration').DataTable({
        Processing: true,
        ServerSide: true,
        ajax: "{{route('person_registration')}}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'full_name',
                name: 'full_name'
            },
            {
                data: 'designation',
                name: 'designation'
            },
            {
                data: 'nationality',
                name: 'nationality'
            },
            {
                data: 'mobile',
                name: 'mobile'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false

            }
        ]

    });

    $('#filterBtn').click(function() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var designation = $('#designation').val();
        var status = $('#status').val();

        // Reload the table with filter parameters
        table.ajax.url("{{ route('person_registration') }}?from_date=" + fromDate + "&to_date=" +
            toDate + "&designation=" + designation + "&status=" + status).load();
    });

});





function confirmDelete(id) {

    var table = $('#person_registration').DataTable();

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("person_registration.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    sweetalert('success_delete');
                    table.ajax.reload();

                    // window.location.href = "{{ route('person_registration') }}";
                    // Swal.fire({
                    // title: 'Deleted!',
                    // text: 'The academic year has been deleted.',
                    // icon: 'success',
                    // timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    // showConfirmButton: false, // Hide the confirm button
                    // willClose: () => {
                    // // Reload the DataTable after the alert closes
                    // $('#academicYearTable').DataTable().ajax.reload();
                    // }
                    // });
                },
                error: function(error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'There was an error in deleting the academic year.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>

@endsection