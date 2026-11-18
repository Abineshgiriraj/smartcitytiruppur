@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span> Document Master</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>

@php

    $get_person_type_options = get_person_type();

@endphp

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="documentMasterform">
                    @csrf
                    @if(isset($acc_year))
                        @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Category Type</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="category_type" name="category_type">
                                <option value="">Select Category Type</option>
                                @foreach($get_person_type_options as $options)
                                    <option value={{$options->unique_id}}>{{$options->person_type}}</option>
                                @endforeach
                            </select>
                            <div id="categoryerror" class="text-danger"></div>
                        </div>

                        <label for="top" class="col-sm-2 col-form-label mt-3">Type Of Proof</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="type_of_proof" name="type_of_proof">
                            <div id="typeofprooferror" class="text-danger"></div>
                        </div>

                        <label for="StatusSelect" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="status" name="status">
                                <option selected>Select Status</option>
                                <option value="1" selected>Active</option>
                                <option value="2">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('document_master') }}'">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <!-- {{ isset($acc_year) ? 'Update' : 'Submit' }} -->
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

    $('#documentMasterform').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Validate the form
        // let isValid = validateForm();
        // if (!isValid) {
        //     return false;
        // }

        var category_type = $('#category_type').val();
        var type_of_proof = $('#type_of_proof').val();

        // Gather form data
        var formData = {
            category_type: $('#category_type').val(),
            type_of_proof: $('#type_of_proof').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        // Determine method and URL based on whether $acc_year is set
        var method = "POST";
        var url = "{{route('document_master_store')}}";

        if (category_type !== '' && type_of_proof !== '') {

            $.ajax({
                url: url,
                method: method,
                data: formData,

                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Category Type create successfully!',
                        timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    }).then(() => {
                        window.location.href = "{{ route('document_master')}}";
                    });
                },

                error: function (xhr) {
                    console.log(xhr.responseText);
                    if (xhr.status === 422) { // Validation error status code
                        sweetalert("already");
                    } else {
                        Swal.fire(
                            'Error!',
                            'An error occurred. Please try again.',
                            'error'
                        );
                    }
                }
            });
        } else {
            if (category_type === '') {
                sweetalert("form_alert");
                $('#categoryerror').text('Category Type is required.');
            } else if(type_of_proof === '') {
                sweetalert("form_alert");
                $('#typeofprooferror').text('Type of Proof is required');
            }
        }
    });

    function validateForm() {
        let isValid = true;
        let accyear = document.getElementById('acc_year');
        let accerr = document.getElementById('acc_err');

        if (accyear.value.trim() === '') {
            accerr.textContent = 'Academic Year must not me empty';
            isValid = false;
        }

        return isValid;
    }
</script>

@endsection