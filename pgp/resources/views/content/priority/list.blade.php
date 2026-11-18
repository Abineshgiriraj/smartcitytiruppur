@extends('layouts/contentNavbarLayout')

@section('title', 'Port Issue Centers - List')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Settings/</span>Priority</h4>
            <a href="{{ route('priority_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
        </div>
    </div>

    <!-- Table to display centers -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dt-responsive nowrap" id="priority_table">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Priority</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>

        $(document).ready(function () {
            $('#priority_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("priority") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                   
                    { 
                        data: 'description', 
                        name: 'description' 
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']]
            });
        });



        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("priority.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#priority_table').DataTable().ajax.reload();
                            Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }


    </script>

@endsection