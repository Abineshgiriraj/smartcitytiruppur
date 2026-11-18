@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Port Area of Access')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Settings /</span> Port Area of Access</h4>
    </div>
</div>

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="port_area_access" method="POST"
                    action="{{ isset($port_area) ? route('port_area_access.update', $port_area->id) : route('port_area_access.store') }}">
                    @csrf
                    @if(isset($port_area))
                    @method('PUT')
                    @endif

                    <div class="row mb-3 com-form">
                        <!-- Area Name -->
                        <label for="area_name" class="col-sm-2 col-form-label mt-3">Area Name</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="area_name" name="area_name"
                                value="{{ old('area_name', $port_area->area_name ?? '') }}" />
                            <div id="area_nameError" class="text-danger"></div>
                        </div>


                        <!-- Short Name -->
                        <label for="short_name" class="col-sm-2 col-form-label mt-3">Short Name</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="short_name" name="short_name"
                                value="{{ old('short_name', $port_area->short_name ?? '') }}" />
                            <!-- <div id="short_nameError" class="text-danger"></div> -->
                        </div>

                        <!-- Description -->
                        <label for="description" class="col-sm-2 col-form-label mt-3">Description</label>
                        <div class="col-sm-4 mt-3">
                            <textarea id="description" class="form-control"
                                name="description">{{ old('description', $port_area->description ?? '') }}</textarea>
                        </div>

                        <!-- Status -->
                        <label for="status" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="Active"
                                    {{ (isset($port_area) && $port_area->status == 'Active') ? 'selected' : '' }}>Active
                                </option>
                                <option value="In Active"
                                    {{ (isset($port_area) && $port_area->status == 'In Active') ? 'selected' : '' }}>In
                                    Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/port_area_access') }}'">Cancel</button>
                        <button type="submit"
                            class="btn btn-primary">{{ isset($port_area) ? 'Update' : 'Submit' }}</button>
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
$('#port_area_access').on('submit', function(e) {
    var area_name = $('#area_name').val();
    var short_name = $('#short_name').val();
    var short_name = $('#description').val();
    var short_name = $('#status').val();

    e.preventDefault();

    var formdata = {
        area_name: $('#area_name').val(),
        short_name: $('#short_name').val(),
        description: $('#description').val(),
        status: $('#status').val(),
        _token: '{{csrf_token()}}',
    }

    if (formdata.area_name !== '') {

        $.ajax({
            url: '{{route("port_area_access.store")}}', // Correct route here
            type: "POST",
            data: formdata,

            success: function(response) {
                const redirectUrl = "{{route('port_area_access')}}";
                sweetalert("create", redirectUrl);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    } else {
        if (formdata.area_name === '') {
            $('#area_nameError').text('Area Name is required.');
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
