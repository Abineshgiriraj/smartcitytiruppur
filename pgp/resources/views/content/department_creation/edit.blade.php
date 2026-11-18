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

            <input type="hidden" id="port_department_id" value="{{ $department->id ?? '' }}">

            @csrf

            <!-- Always use PUT method for updates -->

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="department">Department</label>
                    <input type="text" class="form-control" id="edit_department" name="edit_department"
                        value="{{$department->department}}">

                    <div id="departmentError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">
                    {{$department->description}}
                    </textarea>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ $department->is_active == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $department->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('department') }}'">Cancel</button>
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

        var id = $('#port_department_id').val();

        var department = $('#edit_department').val();


        var formdata = {

            department: $('#edit_department').val(),
            description: $('#description').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        }

        $.ajax({
            type: "POST",
            url: "{{route('department.update','')}}/" + id,
            data: formdata,

            success: function() {
                const redirectUrl = "{{route('department')}}";
                sweetalert("update", redirectUrl);


            }

        });
    });


});
</script>

@endsection