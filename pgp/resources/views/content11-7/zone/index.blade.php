@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Zone Creation')

@section('content')
<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Entry Form /</span> Zone Creation
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ route('zone.create') }}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="zone-table">
                <thead>
                    <tr>
                        <th><b>S.No</b></th>
                        <th><b>Zone Name</b></th>
                        <th><b>Remarks</b></th>
                        <th><b>Action</b></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!--/ Responsive Datatable -->

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

    $(document).ready(function() {
        $('#zone-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('zone.data') }}',
                type: 'GET',
                error: function(xhr, error, code) {
                    console.log(xhr.responseText);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'zone', name: 'zone' },
                { data: 'remarks', name: 'remarks' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true
        });
    });
</script>

@endsection