@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Document Master')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span> Edit Document Master</h4>
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
                    @method('PUT')

                    <div class="row mb-3">
                        <label for="colFormLabelSm" class="col-sm-2 col-form-label mt-3">Category Type</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="category_type" name="category_type">
                                <option value="">Select Category Type</option>
                                @foreach($get_person_type_options as $options)
                                    <option value="{{ $options->unique_id }}" 
                                        {{ $options->unique_id == $documentMaster->category_type ? 'selected' : '' }}>
                                        {{ $options->person_type }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="categoryerror" class="text-danger"></div>
                        </div>

                        <label for="top" class="col-sm-2 col-form-label mt-3">Type Of Proof</label>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="type_of_proof" name="type_of_proof" 
                                value="{{ $documentMaster->type_of_proof }}">
                            <div id="typeofprooferror" class="text-danger"></div>
                        </div>

                        <label for="StatusSelect" class="col-sm-2 col-form-label mt-3">Status</label>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" id="status" name="status">
                                <option value="1" {{ $documentMaster->is_active == 1 ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ $documentMaster->is_active == 2 ? 'selected' : '' }}>InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ route('document_master') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
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

        // Gather form data
        var formData = {
            category_type: $('#category_type').val(),
            type_of_proof: $('#type_of_proof').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        var method = "PUT";
        var url = "{{ route('document_master_update', $documentMaster->id) }}";

        var category_type = $('#category_type').val();
        var type_of_proof = $('#type_of_proof').val();

        if (category_type !== '' && type_of_proof !== '') {
            $.ajax({
                url: url,
                method: method,
                data: formData,

                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Category Type updated successfully!',
                        timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                    }).then(() => {
                        window.location.href = "{{ route('document_master') }}";
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
        }else{
            if (category_type === '') {
                sweetalert("form_alert");
                $('#categoryerror').text('Category Type is required.');
            } else if(type_of_proof === '') {
                sweetalert("form_alert");
                $('#typeofprooferror').text('Type of Proof is required');
            }
        }
    });
</script>

@endsection
