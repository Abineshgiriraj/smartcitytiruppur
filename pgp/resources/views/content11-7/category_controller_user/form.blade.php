@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Entry Form /</span>Controller User</h4>
</div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                             <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Academic Year</label>
                           <select class="form-select" id="acc_year" name="acc_year">
                                <option value="">Select Academic Year</option>
                                <option value="1">2024-2025</option>
                                <option value="2">2025-2026</option>
                            </select>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Zone Name</label>
                            <select class="form-select select2" id="zone" name="zone">
                                <option value="">Select Zone</option>
                                <option value="Madhavaram">Madhavaram</option>
                                <option value="Tondiarpet">Tondiarpet</option>
                                <option value="Royapuram">Royapuram</option>
                                <option value="Thiruvottiyur">Thiruvottiyur</option>
                                <option value="Manali">Manali</option>
                            </select>
                        </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">District Name</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select District</option>
                                <option value="Chennai">Chennai</option>
                                <option value="Salem">Salem</option>
                                <option value="Erode">Erode</option>
                                <option value="Namakkal">Namakkal</option>
                                <option value="Coimbatore">Coimbatore</option>
                                <option value="Karur">Karur</option>
                            </select>
                        </div>
                    <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Taluk Name</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select District</option>
                                <option value="Chennai">Chennai</option>
                                <option value="Salem">Salem</option>
                                <option value="Erode">Erode</option>
                                <option value="Namakkal">Namakkal</option>
                                <option value="Coimbatore">Coimbatore</option>
                                <option value="Karur">Karur</option>
                            </select>
                        </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">School Location</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select School Location</option>
                                <option value="Chennai">Chennai</option>
                                <option value="Salem">Salem</option>
                                <option value="Erode">Erode</option>
                                <option value="Namakkal">Namakkal</option>
                                <option value="Coimbatore">Coimbatore</option>
                                <option value="Karur">Karur</option>
                            </select>
                        </div>
                        
                        
                       
                      
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Date and Time</label>
                            <!--<input type="datetime-local"  class="form-control" id="date_time" name="date_time" />-->
                            <input type="text" id="datetimeInput"  readonly class="form-control" id="date_time" name="date_time" />

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Start Time</label>
                            <input type="time" class="form-control" id="start_time" onkeyuppress="is_number(event)" name="start_time" value="00:00" />
                        </div>
                         <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">End Time</label>
                            <input type="time" class="form-control" id="end_time" onkeyuppress="is_number(event)"  name="end_time" value="00:00" />
                        </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Category Type</label>
                            <select class="form-select" id="category_type" name="category_type">
                                <option value="">Select Category Type</option>
                                <option value="break_fast">Break Fast</option>
                                <option value="lunch">Lunch</option>
                                <option value="schoolin">School In</option>
                                <option value="schoolout">School Out</option></option>
                              
                            </select>
                        </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">User Name</label>
                           
                            <input type="text" id="user_name"  class="form-control" name="user_name" />

                        </div>

                        
                        
                    </div>
                    <div class="btn-float">
                        <button class="btn btn-danger" onclick="window.location.href='{{ url('/category_controller_user_list') }}'"
                            type="button">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function updateDateTime() {
        // Get current date and time
        var now = new Date();

        // Format date
        var year = now.getFullYear();
        var month = (now.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-indexed
        var day = now.getDate().toString().padStart(2, '0');

        // Format time
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var seconds = now.getSeconds().toString().padStart(2, '0');

        // Assemble the datetime string in desired format, for example: YYYY-MM-DD HH:MM:SS
        var datetimeString = year + '-' + month + '-' + day + ' '+ ' ' + hours + ':' + minutes + ':' + seconds;

        // Set the value of the input field
        document.getElementById('datetimeInput').value = datetimeString;
    }

    // Update the date and time every second (1000 milliseconds)
    setInterval(updateDateTime, 1000);

    // Optionally, call updateDateTime once initially to set the initial date and time
    updateDateTime();
    
    
    
    function is_number(event) {

    if((event.keyCode < 46)||(event.keyCode > 57)) event.returnValue = false;

}
</script>




@endsection