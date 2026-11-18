@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Edit User Type')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Department</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="department" method="post">

            <input type="text" id="id" value="{{ $port_area->id ?? '' }}">

            @csrf

            <!-- Always use PUT method for updates -->

            <div class="row mb-3 com-form">
                        <!-- Area Name -->
                        <label for="area_name" class="col-sm-2 col-form-label mt-3">Area Name</label>
                        <div class="col-sm-4 mt-3">
                            <input type="hidden" class="form-control" id="area_name" name="area_name"
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


            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('port_area_access') }}'">Cancel</button>
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
$(document).ready(function() {

    $('#department').on('submit', function(e) {
        e.preventDefault();

        var id = $('#id').val();

        var area_name = $('#area_name').val();


        var formdata = {

            area_name: $('#area_name').val(),
            short_name: $('#short_name').val(),
        description: $('#description').val(),
        status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        }

        $.ajax({
            type: "POST",
            url: "{{route('port_area_access.update','')}}/" + id,
            data: formdata,

            success: function() {
                const redirectUrl = "{{route('port_area_access')}}";
                sweetalert("update", redirectUrl);


            }

        });
    });


});
</script>

@endsection