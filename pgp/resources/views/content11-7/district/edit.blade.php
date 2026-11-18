@extends('layouts/contentNavbarLayout')
@section('title', 'Edit Category')
<!-- @section('title', ' Vertical Layouts - Forms') -->

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Edit Category</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
@php

$zone_name_options = get_zone();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">


                <form id="district_form" action="{{ route('district.update', $district->id) }}" method="POST">
                    <!-- < <form id="districtForm"> -->
                    @csrf
                    <div class="row mb-4 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-fullname">Zone Name</label>
                            <select class="form-select santizeinput" id="zone" name="zone" required>
                                @foreach($zone_name_options as $zone)
                                <option value="{{$zone->unique_id}}"
                                    {{ $zone->unique_id == $district->zone ? 'selected' : '' }}>{{$zone->zone}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">

                            <label class="form-label" for="basic-default-fullname">District Name</label>
                            <input type="text" class="form-control santizeinput" id="district" name="district"
                                value="{{ $district->district}}">
                        </div>

                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Remarks</label>
                            <textarea id="description" class="form-control santizeinput"
                                name="description">{{ $district->description }}</textarea>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{url('/district')}}'">
                            Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
$(document).ready(function() {

    $('.santizeinput').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });
    $('.santizepassword').on('input', function() {
        let value = $(this).val();
        let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _ @]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });


    $('#district_form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: formData,
            success: function(response) {
                const redirectUrl = '{{ route("district") }}';
                sweetalert('update', redirectUrl); // Redirect after successful addition
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    sweetalert('already');
                }
            }


        });

    });
});
</script>



@endsection