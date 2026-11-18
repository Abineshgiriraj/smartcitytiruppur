@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Edit User Type')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span>Edit Vechicle Creation</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <!-- Always use PUT method for updates -->

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Vehicle Registration Number</label>
                    <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no" />
                    <div id="usertypeError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Vehicle Type</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Owner’s Name</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Insurance Details</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Fitness Certificate</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Pollution Under Control (PUC) Certificate</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Valid From</label>
                    <input type="date" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Valid To</label>
                    <input type="date" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Contact Number</label>
                    <input type="text" class="form-control" id="department" name="department" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="usertype">Document Upload:</label>
                    <input type="file" class="form-control" id="file" name="file" />
                    <div id="usertypeError" class="text-danger"></div>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('vehicle_registration') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script>

</script>

@endsection