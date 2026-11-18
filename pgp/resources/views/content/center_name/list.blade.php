@extends('layouts/contentNavbarLayout')

@section('title', 'Port Issue Centers - List')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Issue Centers</h4>
        <a href="{{ route('center_name_add') }}" class="btn btn-primary btn-right">Add New Center</a>
    </div>
</div>

<!-- Table to display centers -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="center_table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Center Name</th>
                        <th>Short Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($centers as $center)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $center->center_name }}</td>
                        <td>{{ $center->short_name }}</td>
                        <td>{{ $center->description }}</td>
                        <td>
                            <span class="badge {{ $center->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($center->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('center_name.edit', $center->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="deleteCenter({{ $center->id }})">Delete</button>
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
        $('#center_table').DataTable(); // Initialize DataTables for sorting, searching, etc.
    });

    // Delete center function
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
    url: '{{ route('center_name.delete', '') }}/' + id, // Correct URL
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

@endsection
