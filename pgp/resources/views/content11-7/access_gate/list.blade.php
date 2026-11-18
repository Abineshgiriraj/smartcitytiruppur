@extends('layouts/contentNavbarLayout')

@section('title', 'Access Gates - List')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Access Gates</h4>
        <a href="{{ route('access_gate.add') }}" class="btn btn-primary btn-right">Add New</a>
    </div>
</div>

<!-- Table to display access gates -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="access_gate_table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Access Gate</th>
                        <th>Short Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($access_gates as $gate)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $gate->access_gate }}</td>
                        <td>{{ $gate->short_name }}</td>
                        <td>{{ $gate->description }}</td>
                        <td>    {{ $gate->status == 1 ? 'Active' : 'Inactive' }}

                        </td>
                        <td>
                            <a href="{{ route('access_gate.edit', $gate->id) }}" class="text-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="text-danger ml-2" onclick="deleteCenter({{ $gate->id }})" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#access_gate_table').DataTable({
            "processing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthChange": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "No data available" // Custom message when no data is present
            }
        });
    });

    // Delete access gate function
    function deleteCenter(id) {
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
                    url: '{{ route('access_gate.destroy', '') }}/' + id, // Correct URL
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                window.location.reload();
                            });
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
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}'
        });
    @endif
</script>
@endsection
