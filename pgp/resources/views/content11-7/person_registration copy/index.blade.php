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
</style>

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

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Designation</th>
                        <th>Description</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    <td>1</td>
                    <td>Supervisor</td>
                    <td>Supervising the all department</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="#" class="text-primary">
                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                            </a>
                            <span class="mx-1"></span>
                            <!-- <button class="btn btn-primary  btn-medium-custom btn-edit">Edit</button> -->
                            <a href="#" class="text-danger ml-2">
                                <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                            </a>
                            <span class="mx-2"></span>
                            <button class="btn btn-primary btn-right btn-small"
                                onclick="window.location.href='{{ route('person_registration_data') }}'" type="button">
                                <i class="i-Add"></i> <span class="lab_add">Renewal</span>
                            </button>
                        </div>
                    </td>

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
    $('#academicYearTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("academic_year_creation_list") }}',
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'from_year',
                name: 'from_year'
            },
            {
                data: 'to_year',
                name: 'to_year'
            },
            {
                data: 'acc_year',
                name: 'acc_year'
            },
            {
                data: 'remarks',
                name: 'remarks'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });
});

function confirmDelete(id) {
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
                url: '{{ route("academic_year_creation.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    sweetalert('success_delete');
                    window.location.href = "{{ route('academic_year_creation_list') }}";
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