@extends('layouts/contentNavbarLayout')

@section('title', 'Vehicle Complaint Registration - List')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Complaint/</span>Vehicle Complaint Registration</h4>
        <a href="{{ route('vehicle_complaint_registration_add') }}" class="btn btn-primary btn-right">Add
            Complaint</a>
    </div>
</div>

<!-- Table to display centers -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="vehicle_table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Complaint Type</th>
                        <th>Vehicle No</th>
                        <th>Reg No</th>
                        <th>Reg Date</th>
                        <!-- <th>Aadhaar No</th> -->
                        <th>DL No</th>
                        <th>Driver Name</th>
                        <th>Status</th>
                        <th>Release Option</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($VehicleComplaint as $complaint)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <!-- <td>{{ $complaint->complaint_type }}</td> -->
                        <td>{{ $complaintTypes[$complaint->complaint_type] ?? 'N/A' }}</td> <!-- Change here -->
                        <td>{{ $complaint->vehicle_no }}</td>
                        <td>{{ $complaint->reg_no }}</td>
                        <td>{{ $complaint->reg_date }}</td>
                        <td>{{ $complaint->dl_no }}</td>
                        <td>{{ $complaint->name }}</td>
                        <td>
                            <span class="badge {{ $complaint->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($complaint->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('vehicle_complaint_registration_list') }}"
                                class="btn btn-sm btn-warning">Release</a>
                        </td>

                        <td>
                            <a href="{{ route('vehicle_complaint_registration.edit', $complaint->id) }}"><i
                                    class="fas fa-edit"></i></a>
                            <a onclick="deleteCenter({{ $complaint->id }})"><i class="fas fa-trash-alt"></i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script> -->

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

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function() {
    $('#vehicle_table').DataTable(); // Initialize DataTables for sorting, searching, etc.
});

// Delete center function
function deleteCenter(id) {
    // alert();
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
                url: '{{ route("vehicle_complaint_registration.delete", ":id") }}'.replace(":id",
                    id), // Correct URL

                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        sweetalert('success_delete');
                        window.location.href =
                            "{{ route('vehicle_complaint_registration_list') }}";
                        // Swal.fire('Deleted!', response.message, 'success').then(() => {
                        //     window.location.reload();
                        // });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });

        }
    });
}
</script>

@endsection