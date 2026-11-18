@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Settings /</span>Ward</h4>
</div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<?php
$get_zone_name = get_zone();
?>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
            <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <span class="text-danger fw-bold">* Mandatory Fields</span>
                    </div>

                    <form id="Ward_form">
                         @csrf
                        <div class="row mb-3">
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="Zone_name">Zone Name<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
    <select class="form-select" id="zone" name="zone">
        <option value="">Select Zone</option>
        @foreach ($get_zone_name as $get_zone)
            <option value="{{ $get_zone->unique_id }}" 
                {{ isset($ward) && $ward->zone == $get_zone->unique_id ? 'selected' : '' }}>
                {{ $get_zone->zone }}
            </option>
        @endforeach
    </select>
</div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="type">Ward Name<span class="text-danger fw-bold" >*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="ward" name="ward" value="{{ isset($ward) ? $ward->ward : '' }}"/>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($ward) ? $ward->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="status">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="is_active" id="is_active">
                                    <option value="1" {{ isset($ward) && $ward->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($ward) && $ward->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('ward') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ isset($ward) ? 'Update' : 'Submit' }}</button>



                        </div>
                    </form>
                </div>
        </div>
    </div>
        </div>
    </div>

</div>
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function () {
  $('#Ward_form').on('submit', function (e) {
    e.preventDefault();

    let formData = $(this).serialize() + '&_token={{ csrf_token() }}';

    // Default = create
    let url = "{{ route('ward.store') }}";
    let method = "POST";

    @if(isset($ward))
      // For update, send the UNIQUE_ID, not the auto-increment id
      url = "{{ route('ward.update', $ward->unique_id) }}";
      method = "PUT";
    @endif

    $.ajax({
      url: url,
      type: method,
      data: formData,
      success: function (response) {
        Swal.fire('Success', response.message, 'success').then(() => {
          window.location.href = "{{ route('ward') }}";
        });
      },
      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          // show the first error
          const firstKey = Object.keys(xhr.responseJSON.errors)[0];
          Swal.fire('Error', xhr.responseJSON.errors[firstKey][0], 'error');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          Swal.fire('Error', xhr.responseJSON.message, 'error');
        } else {
          Swal.fire('Error', 'Something went wrong', 'error');
        }
      }
    });
  });
});
</script>

@endsection