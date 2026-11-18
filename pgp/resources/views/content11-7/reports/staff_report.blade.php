@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Reports /</span> Staff Reports
        </h4>
    </div>
    
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0 g-6 report-filter">
      <div class="col-md-3 user_role"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> Select District </option>
          <option value="Admin">Chennai</option>
          <option value="Author">Erode</option>
          <option value="Editor">Salem</option>
          <option value="Maintainer">Coimbatore</option>
          <option value="Subscriber">Tiruppur</option>
          </select>
          </div>
      <div class="col-md-3 user_plan">
          <select id="UserPlan" class="form-select text-capitalize">
              <option value=""> Select Zone </option>
              <option value="Basic">Zone 1</option>
              <option value="Company">Zone 2</option>
              <option value="Enterprise">Zone 3</option>
              <option value="Team">Zone 4</option>
              </select>
              </div>
               <div class="col-md-3 user_plan">
          <select id="UserPlan" class="form-select text-capitalize">
              <option value=""> Select School </option>
              <option value="Basic">School 1</option>
              <option value="Company">School 2</option>
              <option value="Enterprise">School 3</option>
              <option value="Team">School 4</option>
              </select>
              </div>
      
      <div class="col-md-3">
          <button type="button" class="btn rounded-pill btn-icon btn-primary">
                <span class="tf-icons bx bx-right-arrow-circle bx-22px"></span>
              </button>
      </div>
    </div>
    <hr>
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table border-top" id="example">
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th>Student name</th>
                    <th>Father name</th>
                    <th>D.O.B</th>
                    <th>Standard</th>
                    <th>District</th>
                    <th>Zone</th>
                    <th>School</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- <th></th> -->
                    <td>Dinesh</td>
                    <td>Tamil</td>
                    <td>12-12-2013</td>
                    <td>6</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>Jaya</td>
                    <td>balan</td>
                    <td>01-11-2012</td>
                    <td>7</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government girls school</td>
                </tr>
                <tr>
                    <td>Anusha</td>
                    <td>Jayakumar</td>
                    <td>10-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>karthick</td>
                    <td>dinesh</td>
                    <td>12-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                  <td>deepak</td>
                    <td>Thana</td>
                    <td>12-12-2011</td>
                    <td>8</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                   <td>Divya</td>
                    <td>Annamalai</td>
                    <td>12-12-2010</td>
                    <td>9</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
                <tr>
                    <td>Prabu</td>
                    <td>Sivaraj</td>
                    <td>12-12-2011</td>
                    <td>8</td>
                    <td>Chennai</td>
                    <td>Chennai</td>
                    <td>Government boys school</td>
                </tr>
               


            </tbody>
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

<script src="https://cdn.datatables.net/buttons/3.1.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.html5.min.js"></script>



<script type="text/javascript" class="init">
// new DataTable('#example', {
//     responsive: true,
    
    
// });


new DataTable('#example', {
    layout: {
        topStart: {
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        }
    }
});
</script>

<!-- </div> -->

@endsection