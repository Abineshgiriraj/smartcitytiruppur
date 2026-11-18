@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Settings/</span>Port Category</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="port_category" method="post" action="{{route('harbour_permit_category.update',$port_category->id)}}">
            @csrf
            @if(isset($user_types))
            @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="category">Category</label>
                    <select class="form-select" id="category" name="category">
                        @foreach ( as )

                        @endforeach
                    </select>
                    <div id="categoryError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="sub_category">Sub Category</label>
                    <input type="text" class="form-control" name="sub_category" id="sub_category">
                    <div id="subcategoryError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="descriptions">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Active Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1">Active</option>
                        <option value="2">In Active</option>
                    </select>
                    <div id="statusError" class="text-danger"></div>
                </div>
            </div>

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('harbour_permit_category') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert2.js') }}"></script>

    <script>
    $(document).ready(function() {
        $('#port_category').on('submit', function(e) {
            e.preventDefault();

            var form_data = {
                category: $('#category').val(),
                sub_category: $('#sub_category').val(),
                description: $('#description').val(),
                status: $('#status').val(),
                _token: '{{ csrf_token() }}'
            };

            if (form_data.category !== '' && form_data.sub_category !== '') {
                $.ajax({
                    url: "{{ route('harbour_permit_category.store') }}",
                    method: "POST",
                    data: form_data,
                    success: function(response) {
                        const redirectUrl = "{{ route('harbour_permit_category') }}";
                        sweetalert('create',
                            redirectUrl);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                if (form_data.category === '') {
                    $('#categoryError').text('Category is required.');
                } else {
                    $('#categoryError').text('');
                }

                if (form_data.sub_category === '') {
                    $('#subcategoryError').text('Sub Category is required.');
                } else {
                    $('#subcategoryError').text('');
                }
            }
            if (form_data.category === '' || form_data.sub_category === '') {
                sweetalert("form_alert");
            }

        });
    });



    function getCurrentDateTime() {
        const now = new Date();

        // Format date as YYYY-MM-DD
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(now.getDate()).padStart(2, '0');

        // Format time as HH:MM:SS
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
    </script>

    @endsection