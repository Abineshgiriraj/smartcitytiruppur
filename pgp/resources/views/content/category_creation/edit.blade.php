<!-- resources/views/content/category_creation/edit.blade.php -->
@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Category')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Edit Category</h4>
    </div>
</div>

<style>
.btn-right {
    float: right;
}
</style>

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_creation" action=" {{ route('category.update', $category->id) }}" method="POST">
                    @csrf
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="category_type">Category Type</label>
                            <input type="text" class="form-control  santizeinput" id="category_type"
                                name="category_type" value="{{ $category->category_type}}">

                        </div>
                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="description">Remarks</label>
                            <textarea id="description" class="form-control santizeinput"
                                name="description">{{ $category->description }}</textarea>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/category_list') }}'">Cancel</button>
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
        let sanitizedValue = value.replace(/[^a-zA-Z0-9  _ -]/g, ''); // Remove special characters
        $(this).val(sanitizedValue);
    });

    $('#category_creation').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: formData,

            success: function(response) {
                const redirectUrl = '{{ route("category_list")}}'
                sweetalert('update', redirectUrl);
            },

            // && xhr.responseJSON && xhr.responseJSON.error
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