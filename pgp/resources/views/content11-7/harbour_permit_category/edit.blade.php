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
        <form id="port_category" method="post">
            @csrf
            @if(isset($port_category))
            @method('PUT')
            @endif

            <input type="hidden" id="port_category_id" value="{{ $port_category->id ?? '' }}">
            <!-- Hidden input to store the ID -->

            <div class="row">
                <div class="col-6">
                    <div class="mb-3 mt-2 d-flex align-items-center">
                        <label class="form-label me-4" style="width:100px;" for="category">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="Person" {{$port_category->category == 'Person' ? 'selected' : ''}}>Person
                            </option>
                            <option value="Vehicle" {{$port_category->category == 'Vehicle' ? 'selected' :''}}>Vehicle
                            </option>
                            <option value="Equipment" {{$port_category->category == 'Equipment' ? 'selected' :''}}>Equipment
                            </option>
                        </select>
                        <div id="categoryError" class="text-danger"></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3 mt-2 d-flex align-items-center">
                        <label class="form-label me-4" style="width:100px;" for="sub_category">Sub Category</label>
                        <input type="text" class="form-control" name="sub_category" id="sub_category"
                            value="{{$port_category->sub_category}}">
                        <div id="subcategoryError" class="text-danger"></div>
                    </div>
                 </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="mb-3 mt-2 d-flex align-items-center">
                        <label class="form-label me-4" style="width:100px;" for="description">Description</label>
                        <textarea name="description" id="description"
                            class="form-control">{{$port_category->description}}</textarea>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3 mt-2 d-flex align-items-center">
                        <label class="form-label me-4" style="width:100px;" for="status">Active Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" {{$port_category->status == '1' ? 'selected' : ''}}>Active</option>
                            <option value="2" {{$port_category->status == '2' ? 'selected' :''}}>InActive</option>
                        </select>
                        <div id="statusError" class="text-danger"></div>
                    </div>
                </div>
            </div>
        <!-- </div> -->

            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('harbour_permit_category') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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

            var id = $('#port_category_id').val(); // Get the ID from the hidden input
            var form_data = {
                category: $('#category').val(),
                sub_category: $('#sub_category').val(),
                description: $('#description').val(),
                status: $('#status').val(),
                _token: '{{ csrf_token() }}'
            };

            if (form_data.category !== '' && form_data.sub_category !== '') {
                $.ajax({
                    url: "{{ route('harbour_permit_category.update', '') }}/" +
                        id, // Correctly add the ID to the URL
                    method: "POST",
                    data: form_data,
                    success: function(response) {
                        const redirectUrl = "{{ route('harbour_permit_category') }}";
                        sweetalert('update', redirectUrl);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                // Handle validation errors
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