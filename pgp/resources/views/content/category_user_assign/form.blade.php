@extends('layouts/contentNavbarLayout')
@section('title', ' Vertical Layouts - Forms')
@section('content')


<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Entry Form/</span> Category Users Assign</h4>
</div>
</div>

    
   
    <div class="card">
    <div class="card-body">
            <form class="browser-default-validation">
            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">School ID</label>
                    <input class="form-control" id="school_id" name="school_id"></input>
                    
                </div>
                <div class="col-md-4 mt-4">
                            <button type="button" id="go_button" class="btn btn-primary">Go</button>
                        </div>
                <div id="school_details" class="mt-4" style="display:none;">
                <div class="row">
            <div class="mb-3 col-md-6">
                <label class="form-label" for="basic-default-fullname">Zone Name:</label><br>
                <label class="form-label" for="basic-default-fullname">Zone 1</label>
            </div>
            <div class="mb-3 col-md-6">
                <label class="form-label" for="basic-default-fullname">District Name: </label><br>
                <label class="form-label" for="basic-default-fullname">Chennai</label>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-6">
                <label class="form-label" for="basic-default-fullname">Taluk Name:</label><br>
                <label class="form-label" for="basic-default-fullname">Porur</label>
            </div>
            <div class="mb-3 col-md-6">
                <label class="form-label" for="basic-default-message">School Name:</label><br>
                <label class="form-label" for="basic-default-fullname">Govt Boys school</label>
            </div>
        </div>
                </div>
                <div class="row">
                <div class="col-md-3 mb-3">

                            <label class="form-label" for="basic-default-fullname">Category Type</label>
                            <!--<input type="text" class="form-control" id="district" name="district" />-->
                            <select class="form-select"  name = "category_type" id="category_type">
                                <option value ="break_fast">Break Fast</option>
                                <option value ="Lunch">Lunch</option>
                                <option value ="school_in">School In</option>
                                 <option value ="school_out">School Out</option>
                                
                            </select>
                        </div>
                         <div class="col-md-3 mb-3">

                            <label class="form-label" for="basic-default-fullname">select staff name</label></label>
                            <!--<input type="text" class="form-control" id="district" name="district" />-->
                            <select class="form-select"  name = "category_type" id="category_type">
                                <option value ="break_fast">Kumar</option>
                                <option value ="Lunch">Raja</option>
                                <option value ="school_in">Gowshikan</option>
                                 <option value ="school_out">Yokeshwaran</option>
                                
                            </select>
                        </div>
                        <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">User Name</label>
                    <input class="form-control" id="school_id" name="school_id"></input>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">Password</label>
                    <input class="form-control" type="password" id="school_id" name="school_id"></input>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">Confrimed Password</label>
                    <input class="form-control" type="password" id="school_id" name="school_id"></input>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="btn-float">
                <button type="button" class="btn btn-danger" onclick="window.location.href='{{ url('/category_user_assign_list') }}'">Cancel</button>
                <button type="button" class="btn btn-primary" >Submit</button>
                </div>
            </div>
    </div>
    </div>
  
   <script>
document.getElementById('go_button').addEventListener('click', function() {
    var emis_no = document.getElementById('school_id').value;

    if (emis_no.trim() === '') {
        alert('Please enter the School ID');
    } else {
        document.getElementById('school_details').style.display = 'block';
    }
    
});
</script>     
        
        
        
  


















@endsection