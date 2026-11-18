@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Port Issue Center')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Settings/</span>Edit Port Issue Center</h4>
        <a href="{{ route('center_name_list') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <form id="department" method="post">

<input type="hidden" id="id" value="{{ $port_center->id ?? '' }}">

@csrf
        <!-- <form action="{{ route('center_name.update', $port_center->id) }}" method="POST">
            @csrf
            @method('PUT') This method is required for updating resources -->

            <div class="mb-3">
                <label for="center_name" class="form-label">Center Name</label>
                <input type="text" class="form-control" id="center_name" name="center_name" value="{{ old('center_name', $port_center->center_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="short_name" class="form-label">Short Name</label>
                <input type="text" class="form-control" id="short_name" name="short_name" value="{{ old('short_name', $port_center->short_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $port_center->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" {{ $port_center->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $port_center->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Center</button>
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

    var center_name = $('#center_name').val();


    var formdata = {

        center_name: $('#center_name').val(),
            short_name: $('#short_name').val(),
            description: $('#description').val(),
            // remarks: $('#remarks').val(),
            status: $('#status').val(),
        _token: '{{ csrf_token() }}'
    }

    $.ajax({
        type: "POST",
        url: "{{route('center_name.update','')}}/" + id,
        data: formdata,

        success: function() {
            const redirectUrl = "{{route('center_name_list')}}";
            sweetalert("update", redirectUrl);


        }

    });
});


});
</script>
@endsection
