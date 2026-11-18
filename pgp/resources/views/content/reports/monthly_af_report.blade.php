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
            <span class="text-muted fw-light">Reports /</span> Monthly Afternoon Report  
        </h4>
    </div>
    
</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0 g-6 report-filter">
            <div class="col-md-3 user_role mb-3"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> Academic Year </option>
          <option value="Admin">2024</option>
          <option value="Author">2023</option>
           </select>
          </div>
          <div class="col-md-3 user_role mb-3"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> School Location </option>
          <option value="Admin">Erode</option>
          <option value="Author">Salem</option>
           </select>
          </div>
          <div class="col-md-3 user_role mb-3"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> Zone District  </option>
          <option value="Admin">Erode</option>
          <option value="Author">Tiruppur</option>
           </select>
          </div>
           <div class="col-md-3 user_role mb-3"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> District Name   </option>
           <option value="Admin">Erode</option>
          <option value="Author">Tiruppur</option>
           </select>
          </div>
          <div class="col-md-3 user_role mb-3"><select id="UserRole" class="form-select text-capitalize">
          <option value=""> Taluk  Name   </option>
          <option value="Admin">Bhavani</option>
          <option value="Author">Salem</option>
           </select>
          </div>
      <div class="col-md-3 user_role mb-3">
          
     <input class="form-control" type="month" value="2021-06" id="html5-month-input">
          </div> 
          
      
      
      <div class="col-md-3 mb-3">
          <button type="button" class="btn rounded-pill btn-icon btn-primary">
                <span class="tf-icons bx bx-right-arrow-circle bx-22px"></span>
              </button>
      </div>
        <div class="col-md-3 user_role mb-3"></div>
    </div>
    <hr>
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table border-top" id="example">
            <thead>
                <tr>
                    <tr>
                    <th rowspan="2">S.No</th>
                    <th rowspan="2">Student ID</th>
                    <th rowspan="2">Student Name</th>
                    <th rowspan="2">standards</th>
                   
                    <th colspan="10" style="text-align: center;">Days</th>
                    <th rowspan="2">Total No of Days</th>
                    <th rowspan="2">Total No of Present</th>
                    <th rowspan="2">Total No of Absent</th>
                    </tr>
                      <tr>
                      <th>1</th>
                      <th>2</th>
                      <th>3</th>
                      <th>4</th>
                      <th>5</th>
                      <th>6</th>
                      <th>7</th>
                      <th>8</th>
                      <th>9</th>
                      <th>10</th>
                    </tr>
                  <tr>
                  <td>1</td>
                  <td>STU345</td>
                  <td>Karthika</td>
                  <td>10th</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="red">A</td>
                  <td>9</td>
                  <td>8</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td >1</td>
                  <td>STU346</td>
                  <td>Karthi</td>
                  <td>11th</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="red">A</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="green">P</td>
                  <td class="red">A</td>
                  <td class="red">A</td>
                  <td>9</td>
                  <td>7</td>
                  <td>2</td>
                </tr>
                   
            </thead>
            <tbody>
                
               


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