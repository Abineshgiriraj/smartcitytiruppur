@extends('layouts/contentNavbarLayout')

@section('title', 'Company Creation')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings / </span>Company Type Creation</h4>
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
                <form id="company_form" method="POST"  action="{{ isset($company) ? route('company_creation.update', $company->id) : route('company_creation.store') }}">
                    @csrf
                    @if(isset($company))
                    @method('PUT')
                    @endif
                    @csrf
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="company_name">Company Name</label>
                            <input type="text" id="company_name" class="form-control" name="company_name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="description">Description</label>
                            <input type="text" id="description" class="form-control" name="description">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="inActive">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ url('/company_creation') }}'">Cancel</button>
                        <button type="submit"
                            class="btn btn-primary">{{ isset($company) ? 'Update' : 'Submit' }}</button>
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
//    alert();
    
  
});
$('#company_form').on('submit', function(e) {
    e.preventDefault();
    // alert();
    var company_name = $('#company_name').val();
        var description = $('#description').val();
        var status = $('#status').val();


    e.preventDefault();

    var formdata = {
        company_name: $('#company_name').val(),
        description: $('#description').val(),
        status: $('#status').val(),
        _token: '{{csrf_token()}}',
    }

    if (formdata.area_name !== '') {

        $.ajax({
            url: '{{route("company_creation.store")}}', // Correct route here
            type: "POST",
            data: formdata,

            success: function(response) {
                const redirectUrl = "{{route('company_creation.index')}}";
                sweetalert("create", redirectUrl);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    } else {
        if (formdata.area_name === '') {
            $('#area_nameError').text('Company Name is required.');
        } else {
            $('#area_nameError').text('');
        }
    }

    if (formdata.area_name === '') {
        sweetalert("form_alert");
    }
});
</script>
@endsection