@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings / </span>Port Validity With Fee</h4>
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
                <form id="category_form">
                    @csrf
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="category">Category</label>
                            <select class="form-select" name="category" id="category">
                                <option value="Break Fast">Person</option>
                                <option value="Lunch">Vehicle</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="sub_category">Sub Category</label>
                            <select class="form-select" name="sub_category" id="sub_category">
                                <option value="Break Fast">Heavy</option>
                                <option value="Lunch">cargo</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="area_access">Area Access</label>
                            <select class="form-select" name="area_access" id="area_access">
                                <option value="Break Fast">Administrative Office
                                </option>
                                <option value="Lunch">Inside Custom Bound Area</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="validity">Validity</label>
                            <select class="form-select" name="validity" id="validity">
                                <option value="Break Fast">Monthly</option>
                                <option value="Lunch">Half Yearly</option>
                            </select>
                        </div>
                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="fee">Fee</label>
                            <input type="text" id="fee" class="form-control santizeinput" name="fee">
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/port_permit_validation_with_fee')}}'">Cancel</button>

                        <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{ url('/port_permit_validation_with_fee')}}'">Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>



@endsection