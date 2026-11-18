@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Grievance Module /</span> Repeated Grievance  
        </h4>
    </div>
</div>
<div class="row g-2 align-items-end mb-3">
    <!-- Date From -->
    <div class="col-md-2">
        <label class="form-label">From Date</label>
        <input type="date" id="from_date" class="form-control">
    </div>

    <!-- Date To -->
    <div class="col-md-2">
        <label class="form-label">To Date</label>
        <input type="date" id="to_date" class="form-control">
    </div>

    <!-- Zone -->
    <div class="col-md-2">
        <label class="form-label">Zone</label>
        <select id="zone_filter" class="form-select">
            <option value="">All Zones</option>
            <option value="ZONE - 01">ZONE - 01</option>
            <option value="ZONE - 02">ZONE - 02</option>
        </select>
    </div>

    <!-- Go Button -->
    <div class="col-md-1">
        <button id="filter_btn" class="btn btn-success w-100">
            <i class="bi bi-search"></i> Go
        </button>
    </div>

    <!-- Right-aligned buttons -->
    <div class="col-md-5 d-flex justify-content-end gap-2">
        <button id="print_btn" class="btn btn-outline-primary">
            <i class="bi bi-printer"></i> Print
        </button>
        <button id="excel_btn" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</div>




<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Zone</th>
                        <th>Ward</th>
                        <th>Location</th>
                        <th style="text-align:center;" >Count</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Complaint 1 -->
                    <tr>
                        <td style="text-align:center;">1</td>
                        <td style="text-align:left;"> Public Health
                        </td>
                        <td style="text-align:left;">
                            <div>Dog - Catching</div>
                        </td>
                        <td style="text-align:left;">Zone - I</td>
                        <td style="text-align:left;">
                            <div>Ward-1</div>
                        </td>
                        <td style="text-align:left;">Anna Nesavalar Colony</td>
                        <td style="text-align:center;">
    <a href="#" data-bs-toggle="modal" data-bs-target="#grievanceModal" style="color:black; text-decoration:none;">2</a>
</td>
                       
                    </tr>
                    <tr>
                        <td style="text-align:center;">2</td>
                        <td style="text-align:left;">Accounts
                            
                        </td>
                        <td style="text-align:left;">
                            <div>Group Schemes,Deposits,EMD and Refunds</div>
                        </td>
                        <td style="text-align:left;">Zone - I</td>
                        <td style="text-align:left;">
                            <div>Ward-1</div>
                        </td>
                        <td style="text-align:left;">APR Nagar</td>
                       <td style="text-align:center;">
    <a href="#" data-bs-toggle="modal" data-bs-target="#grievanceModal" style="color:black; text-decoration:none;">3</a>
</td>
                       
                    </tr>
                    <tr>
                        <td style="text-align:center;">3</td>
                        <td style="text-align:left;">Engineering - Works
                        </td>
                        <td style="text-align:left;">
                            <div>Road - Others</div>
                        </td>
                        <td style="text-align:left;">Zone - I</td>
                        <td style="text-align:left;">
                            <div>Ward-4</div>
                        </td>
                        <td style="text-align:left;">Chettipalayam</td>
                        <td style="text-align:center;">
    <a href="#" data-bs-toggle="modal" data-bs-target="#grievanceModal" style="color:black; text-decoration:none;">2</a>
</td>
                       
                    </tr>
                    

                    <!-- Complaint 2 -->
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="modal fade" id="grievanceModal" tabindex="-1" aria-labelledby="grievanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="grievanceModalLabel">Grievance Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>S.No</th>
                <th>Grievance ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Mobile No</th>
                <th>Zone</th>
                <th>Source</th>
                <th>Attachment</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td><div style="font-weight: bold; font-size: 16px;">
                                COM-20250800001
                            </div></td>
                <td>13-08-2025</td>
                <td> <div style="font-weight: bold; font-size: 16px;">Manoj</div>
                            <div style="font-size: 14px;">S.V Colony 2nd Street</div></td>
                <td>9876543210</td>
                <td><div style="font-weight: bold; font-size: 16px;">I</div>
                            <div style="font-size: 14px;">Ward-2</div></td>
                <td>Newspapaer</td>
                <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                <td><button class="btn btn-sm btn-primary">Open</button></td>
              </tr>
              <tr>
                <td>2</td>
                 <td><div style="font-weight: bold; font-size: 16px;">
                                COM-20250800002
                            </div></td>
                <td>13-08-2025</td>
                <td> <div style="font-weight: bold; font-size: 16px;">Gowsi</div>
                            <div style="font-size: 14px;">S.v Colony 2nd Street</div></td>
                <td>9876543222</td>
                <td><div style="font-weight: bold; font-size: 16px;">I</div>
                            <div style="font-size: 14px;">Ward-2</div></td>
                <td>whatsapp</td>
                <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                <td><button class="btn btn-sm btn-primary">Open</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap 5 CSS & JS -->

<script>
function toggleComplaintRow(element) {
    const currentRow = element.closest("tr");
    const detailsRow = currentRow.nextElementSibling;
    if (detailsRow && detailsRow.classList.contains("complaint-details-row")) {
        detailsRow.style.display = (detailsRow.style.display === "none" || detailsRow.style.display === "") 
            ? "table-row" 
            : "none";
    }
}


    
        $(document).ready(function () {
            $('#ward_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("ward") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'zone',
                        name: 'zone'
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



        // function confirmDelete(id) {
        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "This will delete the category.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: '{{ route("zone.delete", ":id") }}'.replace(':id', id),
        //                 type: 'POST',
        //                 data: {
        //                     _method: 'DELETE',
        //                     _token: '{{ csrf_token() }}'
        //                 },
        //                 success: function (response) {
        //                     $('#zone_table').DataTable().ajax.reload();
        //                     Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
        //                 },
        //                 error: function () {
        //                     Swal.fire('Error!', 'Something went wrong.', 'error');
        //                 }
        //             });
        //         }
        //     });
        // }


    </script>

@endsection