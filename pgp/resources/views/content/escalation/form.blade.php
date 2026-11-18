@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings /</span>Escalation</h4>
    </div>
</div>
<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>
<?php
$get_dept_name = get_dept_name();
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

                        <form id="location_form">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="dept_name">Department Name<span
                                            class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <select class="form-select" id="department" name="department">
                                        <option value="">Department name</option>
                                        @foreach ($get_dept_name as $get_department)
                                        <option value="{{ $get_department->unique_id }}"
                                            {{ (isset($escalation) && $escalation->department == $get_department->unique_id) ? 'selected' : '' }}>
                                            {{ formatToCapitalizedWords($get_department->department) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="stage_name">Stage<span
                                            class="text-danger fw-bold">*</span></label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="stage_count"
                                        name="stage_count"
                                        placeholder="Enter Stage Count"
                                        value="{{ isset($escalation) ? $escalation->stage_count : '' }}" />
                                </div>

                                <div class="col-sm-2 mt-3">
                                    <label class="form-label" for="is_active">Status</label>
                                </div>
                                <div class="col-sm-4 mt-3">
                                    <select class="form-select" name="is_active" id="is_active">
                                        <option value="1" {{ isset($escalation) && $escalation->is_active == 1 ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="0" {{ isset($escalation) && $escalation->is_active == 0 ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>

                            </div>

                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ route('escalation') }}'">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="saveUser()">
                                    {{ isset($escalation) ? 'Update' : 'Save' }}
                                </button>

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
    function saveUser() {
        // Collect form data manually (instead of serialize)
        var formData = {
            department: $('#department').val(),
            stage_count: $('#stage_count').val(),
            is_active: $('#is_active').val()
        };

        // Determine method and URL
        var method = "{{ isset($escalation) ? 'PUT' : 'POST' }}";
        var url = "{{ isset($escalation) ? route('escalation.update', $escalation->unique_id) : route('escalation.store') }}";

        // If it's PUT, spoof method (Laravel needs _method=PUT for updates)
        if (method === 'PUT') {
            formData._method = 'PUT';
            method = 'POST';
        }

        // Attach CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // AJAX call
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Escalation ' + (formData._method ? 'updated' : 'added') + ' successfully!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = "{{ route('escalation') }}";
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = Object.values(errors).join('<br>');
                    Swal.fire('Validation Error', errorMessage, 'warning');
                } else {
                    Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                }
            }
        });
    }
</script>


@endsection