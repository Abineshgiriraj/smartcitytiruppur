@extends('layouts/contentNavbarLayout')

@section('title', 'Port Issue Centers - List')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Issue Creation</h4>
        <a href="{{ route('issue_creation_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
    </div>
</div>

<!-- Table to display centers -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered dt-responsive nowrap" >
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>QC Type</th>
                        <th>Issue Category</th>
                        <th>Issue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Physcial QC</td>
                        <td>Document issue</td>
                        <td>typeing issue</td>
                        <td class="text-center">
                                <a href="javascript:void(0);" class="text-primary edit-link " title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-danger delete-link " title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
</tr>
 <tr>
                        <td>2</td>
                        <td>Technical QC</td>
                        <td>functioning not properly</td>
                        <td>not opened</td>
                        <td class="text-center">
                                <a href="javascript:void(0);" class="text-primary edit-link " title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-danger delete-link " title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
</tr>

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
        $('#designation_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("designation") }}',

            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'designation',
                    name: 'designation'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [
                [0, 'asc']
            ]
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the unit.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("designation.delete", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#designation_table').DataTable().ajax.reload();
                        Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }
</script>

@endsection