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

#pass_entry_table.dataTable tbody tr {
    border-top: 1px solid #dee2e6;
}

#pass_entry_table.dataTable thead th,
#pass_entry_table.dataTable tbody td {
    border: 1px solid #dee2e6;
}

#pass_entry_table.dataTable {
    border-collapse: collapse;
    width: 100%;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">HEP Gate Entry/</span> Pass Entry Panalty
        </h4>
    </div>

</div>


<div class="card">
    <div class="card-body">


        <!-- Responsive Datatable -->
        <div class="card">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" id="pass_entry_table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Pass ID</th>
                                <th>Access Area</th>
                                <th>Person / Vehicle</th>
                                <th>Type of Pass</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>In Time</th>
                                <th>Total Hrs</th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
        <script src="{{ asset('public/assets/js/common.js') }}"></script>
        <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

        <link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

        <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
        <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
        <script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

        <script>
        // display a modal (medium modal)
        $(document).on('click', '#mediumButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $('#mediumModal').modal("show");


        });

        $(document).ready(function() {
            $('#pass_entry_table').DataTable({
                paging: true,
                pageLength: 10,
                responsive: true,
            });
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
                        url: '{{ route("pass_entry.delete", ":id") }}'.replace(":id",
                            id), // Correct URL

                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                sweetalert('success_delete');
                                window.location.href =
                                    "{{ route('pass_entry') }}";
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
        $(document).ready(function() {
            // Initialize DataTable with specific settings
            var table = $('#pass_entry_table').DataTable({
                paging: true,
                pageLength: 10,
                responsive: true,
                destroy: true // Allow reinitialization if needed
            });

            // Filter functionality on button click
            $('#filterBtn').on('click', function() {
                // Get values from input fields
                let passId = $('#pass_id').val().trim();
                let agentName = $('#firms_agent').val().trim();

                // Apply search filters directly to DataTable
                table.columns(1).search(passId).columns(2).search(agentName).draw();
            });
        });
        </script>

        @endsection