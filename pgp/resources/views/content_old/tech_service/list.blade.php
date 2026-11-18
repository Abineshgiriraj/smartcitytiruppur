@extends('layouts/contentNavbarLayout')

@section('title', 'Product Creation - List')

@section('content')

    <style>
        #product_table thead th {
            text-align: center !important;
        }
    </style>

    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Service Module/</span>Technical Service</h4>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
           

            <!-- Tabs Content -->
            <div class="tab-content mt-3" id="productTabsContent">
                <!-- Pending Tab Pane -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap" id="product_table_pending">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Complaint Id</th>
                                    <th>Product Name</th>
                                    <th>Service Type</th>
                                    <th>Problem</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><div style="font-weight: bold; font-size: 16px;">COM-123</div>
                                        <div style="font-size: 14px;">25-07-2025</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: bold; font-size: 16px;">Dell latitude 5420 i7</div>
                                        <div style="font-size: 14px;">PRSNO-1234</div>
                                        <div style="font-size: 14px;"></div>

                                    </td>
                                    <td>Onsite</td>
                                    <td>Laptop Issue</td>
                                   <td>
    <a href="{{ route('tech_service.edit', 1) }}" class="text-primary edit-link" title="Edit">
        <i class="fas fa-edit"></i>
    </a>
</td>



                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                <!-- Completed Tab Pane -->
                
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>
       
        $(document).ready(function () {
            $('#product_table_pending').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tech_service') }}",
                    data: { type: 'pending' }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'product_id', name: 'product_id' },
                    { data: 'group_name', name: 'group_name' },
                    { data: 'product_category', name: 'product_category' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
             columnDefs: [
                    { targets: [0, 6, 7, 8], className: 'text-end' }, // Align right
                    { targets: [1, 2, 3, 4, 5, 9], className: 'text-start' } // Align left
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']]
            });

            $('#product_table_completed').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tech_service') }}",
                    data: { type: 'completed' }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'product_id', name: 'product_id' },
                    { data: 'group_name', name: 'group_name' },
                    { data: 'product_category', name: 'product_category' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });



        function deleteProduct(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the product.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("product_creation.delete", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#product_table').DataTable().ajax.reload();
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