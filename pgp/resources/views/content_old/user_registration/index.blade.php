@extends('layouts.contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')
<style>
.btn-small {
    white-space: nowrap;
    padding: 0;
    margin: 0;
    display: inline-flex;
    align-items: center;
    width: 55px;
}

.lab_add {
    white-space: nowrap;
    font-size: 10px;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Online Registration /</span> User Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location='{{ route('user_registration_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-3">
                <select class="form-select" id="idproofSelect" name="idproof">
                    <option selected>Select Designation</option>
                    <option value="Manager">Manager</option>
                    <option value="Employee">Employee</option>
                    <option value="Staff">Staff</option>
                    <option value="Sales Manager">Sales Manager</option>
                    <option value="Driver">Driver</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="id_no" name="id_no" placeholder="Proof/Aadhar No" />
            </div>
            <div class="col-sm-2">
                <button id="openTableButton" class="btn btn-primary">GO</button>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Designation</th>
                        <th>Proof/Aadhar No</th>
                        <th>Mobile No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example of dynamic rows (make sure to replace with your data) -->
                    @foreach($registrations as $registration)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $registration->designation }}</td>
                        <td>{{ $registration->aadhar_no }}</td>
                        <td>{{ $registration->mobile }}</td>
                        
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('user_registration.edit', $registration->id) }}" class="text-primary mr-2">
                                    <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                </a>
                                <a href="#" class="text-danger mr-2" onclick="confirmDelete({{ $registration->id }})">
                                    <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                                </a>
                                <button class="btn btn-primary btn-small" onclick="window.location.href='{{ route('user_renewal', $registration->id) }}'" type="button">
                                    <i class="i-Add"></i>
                                    <span class="lab_add">Renewal</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

<!-- Include Scripts -->
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
    $('#employee_designation').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("academic_year_creation_list") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'from_year', name: 'from_year' },
            { data: 'to_year', name: 'to_year' },
            { data: 'acc_year', name: 'acc_year' },
            { data: 'remarks', name: 'remarks' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
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
                url: '{{ route("user_registration.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    sweetalert('success_delete');
                    window.location.href = "{{ route('academic_year_creation_list') }}";
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire('Error!', 'There was an error in deleting the academic year.', 'error');
                }
            });
        }
    });
}
</script>
@endsection
