@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings / </span>Company Creation</h4>
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

                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="company_name">Company Name</label>
                            <input type="text" id="company_name" class="form-control santizeinput" name="company_name">
                        </div>
                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="description">Description</label>
                            <input type="text" id="description" class="form-control santizeinput" name="description">
                        </div>
                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="inActive">InActive</option>

                            </select>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/company_creation')}}'">Cancel</button>

                        <button type="button" class="btn btn-primary"
                            onclick="window.location.href='{{ url('/company_creation')}}'">Submit</button>

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