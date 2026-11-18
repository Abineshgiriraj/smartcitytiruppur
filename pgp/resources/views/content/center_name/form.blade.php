@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Issue Center</h4>
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
            <form id="center_form" method="POST" action="{{ route('center_name.store') }}">
                    @csrf
                    @if(isset($port_center))
                    @method('PUT')
                    @endif
                    <div class="row mb-3 com-form">
<div class="row">
                            <label class="form-label" for="center_name">Center Name</label>
                            <div class="col-md-4 mb-3">
                            <input type="text" class="form-control" id="center_name" name="center_name"
                                value="{{ isset($port_center) ? $port_center->center_name : '' }}" />
                        </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="short_name">Short Name</label>
                            <input type="text" class="form-control" id="short_name" name="short_name"
                                value="{{ isset($port_center) ? $port_center->short_name : '' }}" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description">{{ isset($port_center) ? $port_center->description : '' }}</textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="active" {{ isset($port_center) && $port_center->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ isset($port_center) && $port_center->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ route('center_name_list') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ isset($port_center) ? 'Update' : 'Submit' }}</button>
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
    $('#center_form').on('submit', function(e) {
        e.preventDefault();

        // Collect form data
        var formData = $(this).serialize();

        // Determine if it's an update or create operation
        var url = "{{ isset($port_center) ? route('center_name.update', $port_center->id) : route('center_name.store') }}";
        var method = "{{ isset($port_center) ? 'PUT' : 'POST' }}";

        // Send AJAX request
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message (SweetAlert or any other method)
                    Swal.fire('Success', response.message, 'success').then(() => {
                        window.location.href = "{{ route('center_name_list') }}";
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                // Handle errors
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    Swal.fire('Error', value[0], 'error');
                });
            }
        });
    });
});
</script>

@endsection
