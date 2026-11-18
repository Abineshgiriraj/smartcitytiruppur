@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings /</span>location</h4>
    </div>
</div>

<style>
    .btn-right { float: right; }
</style>

<?php
    $get_zone_name = get_zone();
    $get_ward_name = get_ward();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row">
    <div class="col-xl">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <span class="text-danger fw-bold">* Mandatory Fields</span>
                        </div>

                        <form id="location_form">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="zone">Zone Name
                                        <span class="text-danger fw-bold">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <select class="form-select" id="zone" name="zone">
                                        <option value="">Select Zone</option>
                                        @foreach ($get_zone_name as $get_zone)
                                            <option value="{{ $get_zone->unique_id }}"
                                                {{ isset($location) && $location->zone == $get_zone->unique_id ? 'selected' : '' }}>
                                                {{ $get_zone->zone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="ward">Ward Name
                                        <span class="text-danger fw-bold">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <div class="input-group">
                                        <select class="form-select" id="ward" name="ward[]" multiple>
                                            <option value="">Select Ward</option>
                                            {{-- dynamically filled on zone change --}}
                                        </select>
                                        <span class="input-group-text">
                                            <input type="checkbox" class="form-check-input mt-0" id="selectAllWard"> All
                                        </span>
                                    </div>
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="location">Location
                                        <span class="text-danger fw-bold">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input type="text" class="form-control" id="location" name="location"
                                           value="{{ isset($location) ? $location->location : '' }}">
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="description">Description</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <textarea class="form-control" id="description" name="description"
                                              oninput="validatetextarea(this)">{{ isset($location) ? $location->description : '' }}</textarea>
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="is_active">Status</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <select class="form-select" name="is_active" id="is_active">
                                        <option value="1" {{ isset($location) && $location->is_active == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ isset($location) && $location->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                        onclick="window.location.href='{{ route('location') }}'">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($location) ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>

                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // Select2
    $('#ward').select2({ placeholder: "Select Ward", width: '80%' });

    // Populate wards on zone change
    $('#zone').on('change', function () {
        let zoneId = $(this).val();
        let wardSelect = $('#ward');
        let selectAllCheckbox = $('#selectAllWard');

        wardSelect.empty();
        selectAllCheckbox.prop('checked', false);

        if (zoneId) {
            let url = `{{ route('getZoneWardData', ':zoneId') }}`.replace(':zoneId', zoneId);
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(ward => {
                            wardSelect.append(new Option(ward.ward, ward.unique_id));
                        });
                    }
                    wardSelect.trigger('change');
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Select/Deselect all wards
    $('#selectAllWard').on('change', function () {
        if (this.checked) {
            const allValues = $('#ward option').map(function(){ return $(this).val(); }).get();
            $('#ward').val(allValues).trigger('change');
        } else {
            $('#ward').val(null).trigger('change');
        }
    });

    // Keep "All" checkbox in sync
    $('#ward').on('change', function () {
        let total = $('#ward option').length;
        let selected = $(this).val() ? $(this).val().length : 0;
        $('#selectAllWard').prop('checked', total > 0 && selected === total);
    });

    // Submit (AJAX)
    $('#location_form').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            zone: $('#zone').val(),
            ward: $('#ward').val(),           // array (multiple)
            location: $('#location').val(),
            description: $('#description').val(),
            is_active: $('#is_active').val(),
            _token: '{{ csrf_token() }}'
        };

        const isUpdate = {!! json_encode(isset($location)) !!};
        const method   = isUpdate ? 'PUT' : 'POST';
        const url      = isUpdate
            ? "{{ isset($location) ? route('location.update', $location->unique_id) : '' }}"
            : "{{ route('location.store') }}";

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function () {
                Swal.fire({
                    title: 'Success!',
                    text: (method === 'POST' ? 'Added' : 'Updated') + ' successfully!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: true,
                }).then(() => {
                    window.location.href = "{{ route('location') }}";
                });
            },
            error: function (xhr) {
                // ✅ No duplicate-specific message; surface backend message if any
                let msg = 'An error occurred. Please try again.';
                try {
                    const res = xhr.responseJSON;
                    if (res && (res.message || res.error)) {
                        msg = res.message || res.error;
                    }
                } catch (e) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

});
</script>
@endsection
