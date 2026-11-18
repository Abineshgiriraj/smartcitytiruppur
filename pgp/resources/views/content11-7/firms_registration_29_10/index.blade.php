@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')
<style>
/* btn style */
/* .btn-right {
        float: right;
    } */

.btn-medium-custom {
    padding: 6px 12px;
    font-size: 14px;
}

table.dataTable th.dt-type-numeric, table.dataTable th.dt-type-date, table.dataTable td.dt-type-numeric, table.dataTable td.dt-type-date {
    text-align: left !important;
}

</style>
@php
$get_firms_registration = get_firms();
@endphp
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">User Firms/</span> Firms Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/firms_registration_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>


</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" value="<?php echo date('Y-m-d');?>" id="from_date">
            </div>
            <div class="col-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" value="<?php echo date('Y-m-d');?>" id="to_date">
            </div>
            <div class="col-2">
                <label class="form-label">Firm Id</label>
                <input type="text" class="form-control" value="" id="firm_id">
            </div>
            <div class="col-2">
                <label for="nationalitySelect" class="form-label">Type of Firms /
                    Agent</label>
                <select class="form-select" id="firms" name="firms">
                    <option selected>Select Firms</option>
                    @foreach ($get_firms_registration as $get_firms)
                    <option value="{{$get_firms->unique_id}}">{{$get_firms->company_name}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-2">
                <label class="form-label">Company Name</label>
                <input type="text" class="form-control" value="" id="name">
            </div>
            <div class="col-2 mt-4">
                <button class="btn btn-primary" id="filterBtn">Go</button>
            </div>


        </div>

        <!-- Responsive Datatable -->
        <div class="card">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="table table-bordered dt-responsive " id="firms_registration">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Firm Id</th>
                                <th>Reg.Date</th>
                                <th>Type of Firm/Agent</th>
                                <th>Comapany Name</th>
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
        <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
        <script src="{{ asset('public/assets/js/common.js') }}"></script>
        <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


        <script type="text/javascript" class="init">
        $(document).ready(function() {
            var table = $('#firms_registration').DataTable({
                Processing: true,
                ServerSide: true,
                ajax: "{{ route('firms_registration') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'firm_id',
                        name: 'firm_id'
                    },
                    {
                        data: 'entry_date',
                        name: 'entry_date'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                var firmId = $('#firm_id').val(); // Corrected variable assignment
                var firms = $('#firms').val();
                var name = $('#name').val();

                // Reload the table with filter parameters
                table.ajax.url("{{ route('firms_registration') }}?from_date=" + fromDate + "&to_date=" +
                    toDate + "&firm_id=" + firmId + "&firms=" + firms + "&name=" + name).load();
            });
        });
        </script>
        <script>
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('
            success ') }}'
        });
        @endif


        function confirmDelete(id) {
            var table = $('#firms_registration').DataTable();

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
                        url: '{{ route("firms_registration.destroy", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            sweetalert('success_delete');
                            table.ajax.reload();
                        },
                        error: function(error) {
                            console.log(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'There was an error in deleting the Designation.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
        </script>

        @endsection