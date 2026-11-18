@extends('layouts/blankLayout')


@section('title', 'Profile & Date Info')

<style>
  table thead {
    background-color: #025392;
    color: #fff;
  }

  table thead th {
    padding: 10px;
    text-align: center;
  }
  .modal-lg-custom {
  max-width: 800px; /* Change value as per requirement */
}
</style>
@section('content')
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Status Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    </head>
    <body>


    <div class="container mt-3">

    <h5 class="mb-3">Status Details</h5>

    @php
    $url = request()->path(); // Get current URL path
    @endphp

    <div class="card mb-3">
    <div class="card-body">
    <div class="row g-3 align-items-end">

      {{-- Show only for completed or repeated --}}
      @if(str_contains($url, 'completed') || str_contains($url, 'repeated'))
    <div class="col-md-2">
      <label for="monthFilter" class="form-label">Select Month</label>
      <input type="month" id="monthFilter" class="form-control">
    </div>
    @endif

      {{-- Show only for pending / process (NO date or month filter) --}}
      {{-- Nothing here because we skip date/month filter --}}

      <!-- Grievance ID -->
      <div class="col-md-2">
      <label for="grievanceId" class="form-label">Grievance ID</label>
      <input type="text" id="grievanceId" class="form-control" placeholder="Enter ID">
      </div>

      <!-- Zone -->
      <div class="col-md-2">
      <label for="zone" class="form-label">Zone</label>
      <select id="zone" class="form-select">
      <option value="">Select</option>
      <option>ZONE I</option>
      <option>ZONE II</option>
      <option>ZONE III</option>
      <option>ZONE IV</option>
      </select>
      </div>

      <!-- Ward -->
      <div class="col-md-2">
      <label for="ward" class="form-label">Ward</label>
      <select id="ward" class="form-select">
      <option value="">Select</option>
      <option>Ward 1</option>
      <option>Ward 2</option>
      <option>Ward 3</option>
      <option>Ward 4</option>
      </select>
      </div>

      <!-- Go Button -->
      <div class="col-md-2">
      <button type="button" class="btn btn-primary w-50">Go</button>
      </div>
    </div>
    </div>
    </div>



    <!-- Table -->
    <table class="table table-bordered">
    <thead>
      <tr>
      <th>S.No</th>
      <th>Date</th>
      <th>Grievance ID</th>
      <th>Grievance</th>
      <th>Zone</th>
      <th>Grievance Person</th>
      <th>Location</th>
      <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr>
      <td>1</td>
      <td>23-08-2025</td>
      <td style="font-weight: bold;">COM-2025080001</td>
       <td>
          <div style="font-weight: bold; font-size: 16px;">Bill Related</div>
          <div style="font-size: 14px;">The citizen has reported a problem concerning accounts and bill payment.</div>

        </td>
      <td><div style="font-weight: bold; font-size: 16px;">Zone I</div>
          <div style="font-size: 14px;">Ward 2</div>
    </td>
     <td><div style="font-weight: bold; font-size: 16px;">Manoj</div>
          <div style="font-size: 14px;">97878676755</div>
    </td>
      <td>1st Street</td>
      <td style="text-align: center"> <a href="#" class="text-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#editStatusModal"> <i class="fas fa-edit"></i> </a> </td>
      </tr>
     <tr>
      <td>2</td>
      <td>22-08-2025</td>
      <td style="font-weight: bold;">COM-2025080002</td>
       <td>
          <div style="font-weight: bold; font-size: 16px;">Bill Related</div>
          <div style="font-size: 14px;">Pension Not Collected concerning accounts and bill payment.</div>

        </td>
      <td><div style="font-weight: bold; font-size: 16px;">Zone II</div>
          <div style="font-size: 14px;">Ward 6</div>
    </td>
     <td><div style="font-weight: bold; font-size: 16px;">Kalai</div>
          <div style="font-size: 14px;">97878676755</div>
    </td>
      <td>7th Street,near RK road</td>
      <td style="text-align: center"> <a href="#" class="text-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#editStatusModal"> <i class="fas fa-edit"></i> </a> </td>
      </tr>
    <tr>
      <td>3</td>
      <td>23-08-2025</td>
      <td style="font-weight: bold;">COM-2025080003</td>
       <td>
          <div style="font-weight: bold; font-size: 16px;">Others</div>
          <div style="font-size: 14px;">The consign Person not repaly proper for pension </div>

        </td>
      <td><div style="font-weight: bold; font-size: 16px;">Zone IV</div>
          <div style="font-size: 14px;">Ward 15</div>
    </td>
     <td><div style="font-weight: bold; font-size: 16px;">Negha</div>
          <div style="font-size: 14px;">97878676755</div>
    </td>
      <td>1st Street</td>
      <td style="text-align: center"> <a href="#" class="text-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#editStatusModal"> <i class="fas fa-edit"></i> </a> </td>
      </tr>
    </tbody>
    </table>

    </div>

    </body>
    </html>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg-custom">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
      <h5 class="modal-title" id="editStatusModalLabel">Update Status</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
      <form id="statusForm">
      <!-- Status -->
      <div class="mb-3">
      <label for="status" class="form-label">Select Status</label>
      <select class="form-select" id="status" name="status" required>
      <option value="">-- Select --</option>
      <option value="Pending">Pending</option>
      <option value="Process">Process</option>
      <option value="Completed">Completed</option>
      <option value="Rejected">Rejected</option>
      </select>
      </div>

      <!-- Remarks -->
      <div class="mb-3">
      <label for="remarks" class="form-label">Remarks</label>
      <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter remarks..."></textarea>
      </div>
      </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      <button type="submit" form="statusForm" class="btn btn-primary">Save</button>
      </div>
    </div>
    </div>
    </div>
@endsection