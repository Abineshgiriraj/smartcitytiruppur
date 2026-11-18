@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Entry Form /</span> Student Creation</h4>
    </div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->

        <div class="card mb-4">
            <div class="card-body">
                <form id="studentForm">
                    <div class="row mb-3">
                      
                          
                            
                        <!--    <div class="col-md-4 mt-4">-->
                        <!--    <button type="button" id="school_go" class="btn btn-primary">Go</button>-->
                        <!--</div>-->
                            <div class="col-md-3 mb-3">
                            <label class="form-label" for="basic-default-fullname">Biometric ID</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select Biometric Id</option>
                                <option value="Chennai">B1123</option>
                                <option value="Salem">B234</option>
                                
                            </select>
                        </div>
                          <div class="col-md-3 mb-3">
                            <label class="form-label" for="basic-default-fullname">Device ID</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select Device Id</option>
                                <option value="Chennai">D1123</option>
                                <option value="Salem">D674</option>
                                
                            </select>
                        </div>
                          <div class="col-md-3 mb-2">
                            <label class="form-label" for="emis_no">School ID</label>
                            <input type="text" class="form-control" id="school_id" name="school_id"  required/>
                            </div>
                          <div class="col-md-2 mt-4">
                           <button type="button" id="school_go" class="btn btn-primary">Go</button>
                        </div>
                        
                        
                        
                        <div id="school_details" class="mt-1 mb-3" style="display:none;">
                          <div class="card bg-lighter">
                                 <div class="card-body">
                                     <span class="fw-medium">School Details</span>
                                     <hr>
                                     <div class="row">
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Zone Name</p>
                                        <p class="text-heading fw-medium">Zone 2</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">District Name</p>
                                        <p class="text-heading fw-medium">Erode</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Taluk Name</p>
                                        <p class="text-heading fw-medium">Perundhurai</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">School Name</p>
                                        <p class="text-heading fw-medium">Govt. Boys School</p>
                                        </div>
                                     </div>
                                 </div>
                                
                            </div>
                        </div>
                        
                        
                          
                        
                        
                     
            
             
            
                        
                        
                        
                        
                        <div class="col-md-3 mb-2">
                            <label class="form-label" for="emis_no">EMIS No.</label>
                            <input type="text" class="form-control" id="emis_no" name="emis_no"  required/>
                            </div><div class="col-md-2 mt-4">
                            <button type="button" id="goButton" class="btn btn-primary">Go</button>
                        </div>
                    </div>
                    
                 <div id="studentDetails" class="mt-4" style="display:none;">
                      <div class="card bg-lighter">
                                 <div class="card-body">
                                     <span class="fw-medium">Student Details</span>
                                     <hr>
                                     <div class="row">
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Student Name</p>
                                        <p class="text-heading fw-medium">E. Murugan</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Date of birth:</p>
                                        <p class="text-heading fw-medium"> 31-07-1998</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Current standard </p>
                                        <p class="text-heading fw-medium">12th</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Group Name</p>
                                        <p class="text-heading fw-medium">Computer Science</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">Medium</p>
                                        <p class="text-heading fw-medium">Tamil</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">School Name</p>
                                        <p class="text-heading fw-medium">Govt. Boys School</p>
                                        </div>
                                        <div class="col-3 school-det">
                                        <p class="mb-2 small">School Block</p>
                                        <p class="text-heading fw-medium">Erode</p>
                                        </div>
                                          <div class="col-3 school-det">
                                        <p class="mb-2 small">School District</p>
                                        <p class="text-heading fw-medium">Erode</p>
                                        </div>
                                     </div>
                                 </div>
                                
                            </div>
                     
                     
                     
                     
                     
                   
                    </div>
                    <div class="btn-float">
                        <button class="btn btn-danger" onclick="window.location.href='{{ url('/student_list') }}'"
                            type="button">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
        </div>
   
<script>
document.getElementById('goButton').addEventListener('click', function() {
    var emis_no = document.getElementById('emis_no').value;

    if (emis_no.trim() === '') {
        alert('Please enter the EMIS number');
    } else {
        document.getElementById('studentDetails').style.display = 'block';
    }
    
});
document.getElementById('school_go').addEventListener('click', function() {
    var school_id = document.getElementById('school_id').value;

    if (school_id.trim() === '') {
        alert('Please enter the School ID');
    } else {
        document.getElementById('school_details').style.display = 'block';
    }
});
</script>
@endsection
