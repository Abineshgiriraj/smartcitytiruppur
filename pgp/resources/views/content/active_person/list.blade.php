@extends('layouts/contentNavbarLayout')

@section('title', 'Profile & Date Info')

<style>
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 5px 0 15px 0;
}
.profile-left h5 {
    font-size: 20px;
    font-weight: bold;
    margin: 0 0 5px 0;
}
.profile-left p {
    font-size: 15px;
    margin: 2px 0;
    color: #333;
}
.datetime-section {
    text-align: right;
}
#day {
    font-weight: bold;
    font-size: 16px;
    margin: 0;
}

/* Status Cards */
.status-card {
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    background-color: #fff;
    color: #0d6efd;
    font-weight: bold;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}
.status-card:hover {
    transform: translateY(-5px);
}
.status-card h6 {
    margin-bottom: 10px;
    font-size: 16px;
}
.status-card p {
    font-size: 22px;
    margin: 0;
    color:#025392;
}

/* Modal Style (White Static) */
.modal-content {
    background-color: #fff !important;
    color: #000 !important;
}
.modal-header {
    border-bottom: 1px solid #ddd;
}
.modal-footer {
    border-top: 1px solid #ddd;
}
</style>

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="row mb-3 page-hed">
    <div class="col-sm-12">
        <!-- Card -->
        <div class="card shadow-sm rounded-3">
            <div class="card-body">

                <!-- Top Row: Profile + Date -->
                <div class="profile-header">
                    <!-- Left Section -->
                    <div class="profile-left">
                        <h5>Kalaivani.K</h5>
                        <p class="mb-0"><i class="bi bi-telephone"></i> +91 99999 88888</p>
                        <p class="mb-0"><i class="bi bi-envelope"></i> kalai@123</p>
                        <p class="mb-0"><i class="bi bi-people-fill"></i> Department : <strong>Accounts</strong> </p>
                        <p class="mb-0"><i class="bi bi-card-list"></i> Category : <strong>Bill Related , others</strong></p>
                    </div>

                    <!-- Right Section -->
                    <div class="datetime-section">
                        <p id="datetime" class="fw-bold mb-0"></p>
                        <p id="day"></p>
                    </div>
                </div>

                <hr>
<div class="row pb-3">
    <div class="mb-6 col-lg-5 col-xl-5 col-5 mb-0">
        <div class="d-flex align-items-center py-2 px-3 border rounded" style="    background-color: #eefaff; border-color: #cdedff !important;">
            <div class="avatar flex-shrink-0 me-3">
              <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/icons/unicons/briefcase.png" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-3">
                <p class="mb-0 text-heading"> Register grievance  </p>
              </div>
              <div class="user-progress">
             <div class="d-flex justify-content-center">
  <h5 class="mb-0 text-primary" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#countModal">
    17
  </h5>
</div>
              </div>
            </div>
          </div>

        <!-- <div class="content-right">
              <p class="mb-0 fw-medium">Register grievance :</p>
              <h4 class="text-primary mb-0">15</h4>
            </div> -->
    </div>
    <div class="mb-6 col-lg-5 col-xl-5 col-5 mb-0"></div>
    <div class="mb-6 col-lg-2 col-xl-2 col-2 mb-0 align-right">
        <label class="form-label" for="form-repeater-1-4">Acedemic Year</label> 
        <select id="form-repeater-1-4" class="form-select">
            <option value="Designer">2024 - 2025</option>
            <option value="Developer">2025 - 2026</option>
            <option value="Tester">2027 - 2028</option>
            <option value="Manager">2029 - 2030</option>
        </select>
    </div>

</div>
                <!-- Status Cards -->
                <div class="row text-center">
    <div class="col-md-3 mb-3">
        <div class="status-card" onclick="openStatusWindow('pending')">
            <h6>Pending</h6>
            <p>2</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="status-card" onclick="openStatusWindow('processing')">
            <h6>Processing</h6>
            <p>3</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="status-card" onclick="openStatusWindow('completed')">
            <h6>Completed</h6>
            <p>5</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="status-card" onclick="openStatusWindow('repeated')">
            <h6>Repeated</h6>
            <p>7</p>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="editStatusModalLabel">Update Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="statusForm">
          <div class="mb-3">
            <label for="status" class="form-label">Select Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="">-- Select --</option>
              <option value="Pending">Pending</option>
              <option value="Process">Process</option>
              <option value="Completed">Completed</option>
            </select>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="statusForm" class="btn btn-primary">Save </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="countModal" tabindex="-1" aria-labelledby="countModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Wide modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="countModalLabel">Overall Grievance Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        
        <!-- 🔎 Search/Filter Row -->
        <div class="row g-3 mb-3"> <!-- g-3 adds spacing -->
          <!-- Grievance ID -->
          <div class="col-md-3">
            <label for="grievanceId" class="form-label">Grievance ID</label>
            <input type="text" id="grievanceId" class="form-control" placeholder="Enter ID">
          </div>

          <!-- Zone -->
          <div class="col-md-3">
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
          <div class="col-md-3">
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
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-primary w-100">Go</button>
          </div>
        </div>

        <!-- 🔎 Table -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
              <tr>
                <th>S.No</th>
                <th>Date</th>
                <th>Grievance ID</th>
                <th>Grievance</th>
                <th>Zone</th>
                <th>Grievance Person</th>
                <th>Location</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>23-08-2025</td>
                <td class="fw-bold">COM-2025080001</td>
                <td>
                  <div class="fw-bold fs-6">Bill Related</div>
                  <div class="text-muted small">The citizen has reported a problem concerning accounts and bill payment.</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Zone I</div>
                  <div class="text-muted small">Ward 2</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Manoj</div>
                  <div class="text-muted small">97878676755</div>
                </td>
                <td>1st Street</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
              </tr>

              <tr>
                <td>2</td>
                <td>22-08-2025</td>
                <td class="fw-bold">COM-2025080002</td>
                <td>
                  <div class="fw-bold fs-6">Bill Related</div>
                  <div class="text-muted small">Pension Not Collected concerning accounts and bill payment.</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Zone II</div>
                  <div class="text-muted small">Ward 6</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Kalai</div>
                  <div class="text-muted small">97878676755</div>
                </td>
                <td>7th Street, near RK road</td>
                <td><span class="badge bg-success">Completed</span></td> <!-- ✅ fixed missing </td> -->
              </tr>

              <tr>
                <td>3</td>
                <td>23-08-2025</td>
                <td class="fw-bold">COM-2025080003</td>
                <td>
                  <div class="fw-bold fs-6">Others</div>
                  <div class="text-muted small">The consign Person not reply proper for pension</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Zone IV</div>
                  <div class="text-muted small">Ward 15</div>
                </td>
                <td>
                  <div class="fw-bold fs-6">Negha</div>
                  <div class="text-muted small">97878676755</div>
                </td>
                <td>1st Street</td>
                <td><span class="badge bg-info text-dark">Processing</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
 function openStatusWindow(status) {
    let url = "{{ url('/active_person') }}/" + status;
    window.open(
        url,
        "StatusWindow",
        "width=1200,height=700,scrollbars=yes,resizable=yes"
    );
}
// Format date as DD-MM-YYYY HH:MM AM/PM
function formatDate(date) {
    let d = String(date.getDate()).padStart(2, '0');
    let m = String(date.getMonth() + 1).padStart(2, '0');
    let y = date.getFullYear();

    let hours = date.getHours();
    let minutes = String(date.getMinutes()).padStart(2, '0');
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;

    return `${d}-${m}-${y} ${hours}:${minutes} ${ampm}`;
}

function updateDateTime() {
    const now = new Date();
    document.getElementById("datetime").innerText = formatDate(now);
    document.getElementById("day").innerText = now.toLocaleDateString('en-US', { weekday: 'long' });
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
