@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Access IN & OUT/</span>Vehicle Access Center</h4>
    </div>
</div>
<br id="usertypeForm" method="post" onsubmit="return submitForm(event);">

<div class="card">
    <div class="card-body">
        @csrf
        @if(isset($user_types))
        @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-3">
                <label class="form-label" for="rfid_no">Center Name - Gate 1</label>
                <label class="form-label" for="rfid_no">Center ID - gate001</label>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">RFID Number</label>
                <input type="text" class="form-control" id="vech_reg_no" name="vech_reg_no" />
                <div id="usertypeError" class="text-danger"></div>

            </div>
            <div class="mb-3 col-md-3 mt-4">
                <button type="submit" class="btn btn-primary" onclick="get_div()">Go</button>
            </div>
        </div>
    </div>
</div>
</br>

<div class="card" id="div-style" style="display:none;">
    <div class="card-body">
        <div class="row">
            <div class="mb-3 col-md-12">
                <label class="form-label" for="usertype">Vehicle Information</label>
                <hr>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Owner’s Name</label>
                <h6>Ravi</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Contact Number</label>
                <h6>9007837368</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Insurance Details</label>
                <h6>Monthly Insurance</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype"> Insurance Valid From</label>
                <h6>01-01-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Insurance Valid To</label>
                <h6>31-01-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>


            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Fitness Certificate</label>
                <h6>FC-889897879</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype"> Fitness Valid From</label>
                <h6>01-01-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Fitness Valid To</label>
                <h6>31-12-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Pollution Under Control (PUC) Certificate</label>
                <h6>PUC-793728</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Pollution Valid From</label>
                <h6>01-01-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Pollution Valid To</label>
                <h6>31-12-2024</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>

            <div class="mb-3 col-md-3"></div>

            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Check In</label>
                <h6>08:00 AM</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Check Out</label>
                <h6>12:00 PM</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="usertype">Total Time</label>
                <h6>04:00 Hrs</h6>
                <div id="usertypeError" class="text-danger"></div>
            </div>
        </div>
    </div>
</div>
</form>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>
    function get_div() {
        var div = document.getElementById("div-style");
        if (div) {
            div.style.display = "block";
        }
    }
</script>

@endsection