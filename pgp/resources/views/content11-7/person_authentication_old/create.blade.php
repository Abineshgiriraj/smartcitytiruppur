@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Authentication/</span>Person Authentication</h4>
    </div>
</div>
<form id="usertypeForm" method="post" onsubmit="return submitForm(event);"></form>
<div class="card">
    <div class="card-body">
        @csrf
        @if(isset($user_types))
        @method('PUT')
        @endif
        <h5>Personal Details :</h5>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Name:&nbsp;&nbsp; Monish</label>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Mobile Number:&nbsp;&nbsp; 9862676721</label>
            </div>
        </div>
        <hr>
        <h5>Aadhar Validation :</h5>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Aadhaar Number: &nbsp;&nbsp; 9878 9778 7655</label>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">
                    Aadhar Preview: &nbsp;&nbsp;
                    <img src="resources/views/content/person_authentication/pdf_image.png" alt="Aadhar Card Preview"
                        style="width: 40px; height: 40px;">
                </label>
            </div>
        </div>
        <hr>
        <h5>Access :</h5>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Date:&nbsp;&nbsp; 12/12/2024</label>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Access Area: &nbsp;&nbsp; Front Office</label>
            </div>
        </div>
        <hr>
        <h5>Pay :</h5>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department">Fees: &nbsp; <b>250/-</b></label>
            </div>
        </div>
    </div>
</div>
</br>
<div class="card">
    <div class="card-body">
        <div class="row mb-3 com-form">
            <div class="col-md-4 mb-3">
                <label class="form-label" for="app_pcs">Status</label>
                <select class="form-select" name="app_pcs" id="app_pcs" onchange="toggleDescription()">
                    <option value="">Select Status</option>
                    <option value="Approve">Approve</option>
                    <option value="Reject">Reject</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label" for="app_date">App Date</label>
                <input type="date" class="form-control santizeinput" id="app_date" name="app_date" />
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label" for="auth_by">Authenticate By</label>
                <input type="text" class="form-control santizeinput" id="auth_by" name="auth_by" value="Shankar"
                    readonly />
            </div>
        </div>
        <div class="row mb-3 com-form" id="descriptionContainer" style="display: none;">
            <div class="col-md-12 mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" class="form-control santizeinput" name="description"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="btn-float">
                <button type="button" class="btn btn-danger"
                    onclick="window.location.href='{{ route('person_authentication') }}'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>

    </div>
</div>
</form>
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    function toggleDescription() {
        const selectElement = document.getElementById('app_pcs');
        const descriptionContainer = document.getElementById('descriptionContainer');

        if (selectElement.value === 'Approve') {
            descriptionContainer.style.display = 'none';
        } else {
            descriptionContainer.style.display = 'block';
        }
    }
</script>

@endsection