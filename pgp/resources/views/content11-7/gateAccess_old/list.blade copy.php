@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

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
            <span class="text-muted fw-light">Issue Center/</span> Access Gate IN
        </h4>
    </div>
    <!-- <div class="col-sm-6">
        @if(session('user_type') == '66eaeaf79982060867')
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/pass_entry_from')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
        @else
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/PassEntry')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
        @endif
    </div> -->


</div>

<!-- Responsive Datatable -->
<div class="card">
<div class="card-header border-bottom">
    
    <div class=" align-items-center row gap-4 gap-md-0 g-6">
      <div class="col-md-4 user_role">
	  <label for="email" class="form-label">RFT No</label>
	 <input type="text" class="form-control" id="text" name="text"  autofocus="">
	  </div>
	    <div class="col-md-4 user_role align-self-center mt-4">
		<button type="button" class="btn btn-primary">Go</button>
		</div>
    </div>
  </div>
  
  <div class="card mb-6">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Personal Details</h5> 
      </div>
	  
      <div class="card-body">
	   <form class="fomr-new">
	  <div class="row">
	  <div class="col-md-4">
       
          <div class="row mb-1">
            <label class="col-sm-6 ">Academic Year</label>
            <div class="col-sm-6">
            <h6>2024-2025</h6>
            </div>
          </div>
             <div class="row mb-1">
            <label class="col-sm-6 ">Register Number:</label>
            <div class="col-sm-6">
            <h6>12343345345345</h6>
            </div>
          </div>
            <div class="row mb-1">
            <label class="col-sm-6 ">Company</label>
            <div class="col-sm-6">
            <h6>Ascent</h6>
            </div>
          </div>
            <div class="row mb-1">
            <label class="col-sm-6 ">Aadhaar No</label>
            <div class="col-sm-6">
            <h6>122324343</h6>
            </div>
          </div>
            <div class="row mb-1">
            <label class="col-sm-6 ">Nationality</label>
            <div class="col-sm-6">
            <h6>Indian</h6>
            </div>
          </div>
        
       
		</div>
		
			  <div class="col-md-4">
       
          <div class="row mb-1">
            <label class="col-sm-6 ">Country</label>
            <div class="col-sm-6">
            <h6>India</h6>
            </div>
          </div>
             <div class="row mb-1">
            <label class="col-sm-6 ">Mobile</label>
            <div class="col-sm-6">
            <h6>9707979797</h6>
            </div>
          </div>
            <div class="row mb-1">
            <label class="col-sm-6 ">Email ID</label>
            <div class="col-sm-6">
            <h6>demo@gmail.com</h6>
            </div>
          </div>
            <div class="row mb-1">
            <label class="col-sm-6 ">Blood Group</label>
            <div class="col-sm-6">
            <h6>0+ve</h6>
            </div>
          </div>
           
        
       
		</div>
		
		
		<div class="col-md-4" >
		<div class=" align-items-sm-center gap-6 " style="float: inline-end;text-align: center;">
          <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/avatars/1.png" alt="user-avatar" class=" rounded" id="uploadedAvatar" style="width: 49%;
    margin-bottom: 10px;">
          <div class="button-wrapper">
          

           <p class=" mb-1">Name: <b>Shamus </b></p>
		<p class=" mb-1">Designation:  <b>Person </b></p>
          </div>
        </div>
		<!--<div class="profile">
		<img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/avatars/1.png" class="rounded mb-3">
		<p class=" mb-1">Name: Shamus</p>
		<p class=" mb-1">Designation: Person</p>
		</div>-->
		</div>
		 </form>
		
      </div>
	  </div>
    </div>
    <div class="card-body">
	
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="pass_entry_table">
            <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Gate ID</th>
                        <th>Area Access</th>
                        <th>Vehicle/Person</th>
                        <th>Pass ID</th>
                        <th>PAss Type</th>
                        <th>Date</th>
                        <th>IN Time</th>
                        <th>Out Time</th>
						<th>Validity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>5363000</td>
                        <td>Inside Custom Bound Area(SBW)</td>
                        <td>TN23AS1233</td>
                        <td>00001</td>
                        <td>Day</td>
                        <td>25-10-2024</td>
                        <td>09:21 AM</td>
                        <td>-</td>
						<td><span class="badge bg-label-success"> <i class=" bx bx-check"></i> </span>
						</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>53636363</td>
                        <td>Administrative Office</td>
                        <td>Karthick</td>
                        <td>00031</td>
                        <td>Day</td>
                        <td>25-10-2024</td>
                        <td>09:30 AM</td>
                        <td>-</td>
						<td><span class="badge bg-label-danger"> <i class=" bx bx-x"></i> </span>
						</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>53636900</td>
                        <td>Inside Custom Bound Area(SBW)</td>
                        <td>TN30AC0989</td>
                        <td>00031</td>
                        <td>Month</td>
                        <td>25-10-2024</td>
                        <td>09:41 AM</td>
                        <td>-</td>
						<td><span class="badge bg-label-danger"> <i class=" bx bx-x"></i> </span>
						</td>
                    </tr>
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

<script>
// display a modal (medium modal)
$(document).on('click', '#mediumButton', function(event) {
    event.preventDefault();
    let href = $(this).attr('data-attr');
    $('#mediumModal').modal("show");


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
</script>

@endsection