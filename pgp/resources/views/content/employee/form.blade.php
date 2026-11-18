@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Entry Form /</span> Staff Creation</h4>
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
                            <label class="form-label" for="basic-default-fullname">Employee Name</label>
                            <input type="text" class="form-control" id="emp_name" name="emp_name" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Employee ID</label>
                            <input type="text" class="form-control" id="emp_id" name="emp_id" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Designation</label>
                            <select class="form-select" id="district" name="district">
                                <option value="">Select Designation</option>
                                <option value="acct">Accountant</option>
                                <option value="clerk">Clerk</option>
                                <option value="teacher">Teacher</option>
                               
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
                            <label class="form-label" for="basic-default-fullname">School (or) Location Name</label>
                            <!-- <input type="text" class="form-control" id="school" name="school" /> -->
                            <select class="form-select select2" id="school" name="school">
                                <option value="">Select School</option>
                                <option value="govt_boys_school">Govt. Boys School</option>
                                <option value="govt_high_sec_school">Govt. Higher Sec. School</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Date of Join</label>
                            <input type="date" class="form-control" id="doj" name="doj" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Employee Contact Number</label>
                            <input type="text" class="form-control" id="emp_contact" name="emp_contact" />
                        </div>
                    </div>
                    <div class="btn-float">
                        <button class="btn btn-danger" onclick="window.location.href='{{ url('/employee_list') }}'"
                            type="button">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection