@extends('layouts/contentNavbarLayout')

@section('title', isset($company) ? 'Edit Company' : 'Company Creation Form')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Settings / </span>{{ isset($company) ? 'Edit Company' : 'Company Type Creation' }}</h4>
    </div>
</div>

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
               
                    <form id="company_form" method="post">

<input type="hidden" id="id" value="{{ $company->id ?? '' }}">

@csrf

                    <div class="row mb-3 com-form">
                        <!-- Company Name -->
                        <label for="company_name" class="col-sm-2 col-form-label mt-3">Company Name</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" id="company_name" class="form-control" name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}">
                            <div id="company_nameError" class="text-danger"></div>
                        </div>

                        <!-- Description -->
                        <label for="description" class="col-sm-2 col-form-label mt-3">Description</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" id="description" class="form-control" name="description" value="{{ old('description', $company->description ?? '') }}">
                        </div>

                        <!-- Status -->
                        <label for="status" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="Active" {{ (isset($company) && $company->status == 'Active') ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ (isset($company) && $company->status == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div id="statusError" class="text-danger"></div>
                        </div>
                    </div>


                    <div class="btn-float">
                        <!-- <button type="submit" class="btn btn-primary">{{ isset($company) ? 'Update' : 'Submit' }}</button> -->
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ route('company_creation.index') }}'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    
$(document).ready(function() {

$('#company_form').on('submit', function(e) {
    e.preventDefault();

    var id = $('#id').val();

    // var department = $('#edit_department').val();
    var company_name = $('#company_name').val();
        var description = $('#description').val();
        var status = $('#status').val();


    var formdata = {

        company_name: $('#company_name').val(),
        description: $('#description').val(),
        status: $('#status').val(),
        _token: '{{ csrf_token() }}'
    }

    $.ajax({
        type: "POST",
        url: "{{route('company_creation.update','')}}/" + id,
        data: formdata,

        success: function() {
            const redirectUrl = "{{route('company_creation.index')}}";
            sweetalert("update", redirectUrl);


        }

    });
});


});
</script>
@endsection