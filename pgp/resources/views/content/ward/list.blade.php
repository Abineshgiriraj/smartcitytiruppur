@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
  /* btn style */
  .btn-right { float: right; }

  /* optional: keep rows tidy */
  #ward_datatable td { vertical-align: middle; }

  /* add horizontal grid lines between rows */
  #ward_datatable th, 
  #ward_datatable td {
      border: 1px solid #dee2e6 !important;  /* ✅ Bootstrap table border color */
  }

  /* status colors */
  .status-active   { color:#28a745; font-weight:600; }
  .status-inactive { color:#dc3545; font-weight:600; }
</style>

<?php $get_zone_name = get_zone(); ?>

@section('content')
<div class="row mb-3 page-hed">
  <div class="col-sm-6">
    <h4 class=""><span class="text-muted fw-light">Settings /</span> Ward</h4>
  </div>
  <div class="col-sm-6">
    <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/ward_add') }}'" type="button">
      <i class="i-Add"></i> Add New
    </button>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="card-datatable table-responsive">
      <table class="dt-responsive table table-bordered border-top" id="ward_datatable">
        <thead>
          <tr>
            <th class="text-center">S.No</th>
            <th class="text-center">Zone Name</th>
            <th class="text-center">Ward Name</th>
            <th class="text-center">Description</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function () {
  $('#ward_datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("ward") }}',
    columns: [
      { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false, className: "text-center" },
      { data: 'zone_name', name: 'zone_name', className: "text-start" },
      { data: 'ward', name: 'ward', className: "text-start" },
      { data: 'description', name: 'description', className: "text-center" },
      {
        data: 'is_active',
        name: 'is_active',
        className: "text-center",
        render: function(data) {
          return (String(data) === '1')
            ? '<span class="status-active">Active</span>'
            : '<span class="status-inactive">Inactive</span>';
        }
      },
      { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" }
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
        url: '{{ route("ward.delete", ":id") }}'.replace(':id', id),
        type: 'POST',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function () {
          $('#ward_datatable').DataTable().ajax.reload();
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
