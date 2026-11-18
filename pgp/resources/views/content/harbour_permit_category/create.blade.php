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
        <form id="portcategory" method="post">
            @csrf
            @if(isset($user_types))
            @method('PUT')
            @endif

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="department">Category</label>
                    <select class="form-control" id="category" name="category">
                        <option value="" disabled selected>Select a category</option>
                        <option value="Personal">Personal</option>
                        <option value="Vehicle">Vehicle</option>
                    </select>
                    <div id="usertypeError" class="text-danger"></div>
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label" for="subcategory">Sub Category</label>
                    <input type="text" class="form-control" id="subcategory" name="subcategory">
                    <!-- <select class="form-control" id="subcategory" name="subcategory">
                    <option value="" disabled selected>Select a subcategory</option>
                    </select> -->
                    <div id="subcategoryError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                    <!-- <div id="statusError" class="form-text text-danger"></div> -->
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="department">Active Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                    <div id="usertypeError" class="text-danger"></div>
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
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert2.js') }}"></script>

<script>
function updateSubCategory() {
    const category = document.getElementById('department').value;
    const subcategorySelect = document.getElementById('subcategory');

    // Clear existing options
    subcategorySelect.innerHTML = '<option value="" disabled selected>Select a subcategory</option>';

    let options = [];
    switch (category) {
        case 'Personal':
            options = [
                'Employees', 'Pensioners', 'Drivers', 'Cleaners',
                'Operators', 'Port Users', 'PPP Operators',
                'Casual Labors', 'Contractors', 'Contract Labors', 'Visitors'
            ];
            break;
        case 'Heavy Vehicle':
            options = ['Import Container Carriers', 'Export Container Carriers', 'Cargo Carriers'];
            break;
        case 'Light Vehicle':
            options = ['Passenger Carriers', 'Two Wheelers'];
            break;
        case 'Equipments':
            options = ['Cranes', 'FELS', 'Excavators', 'Grabs', 'Generators'];
            break;
    }

    // Populate the subcategory dropdown
    options.forEach(option => {
        const opt = document.createElement('option');
        opt.value = option;
        opt.textContent = option;
        subcategorySelect.appendChild(opt);
    });
}
$(document).ready(function() {


    $('#portcategory').on('submit', function(e) {
        e.preventDefault();
        var description = $('#description').val();

        var formdata = {
            category: $('#category').val(),
            sub_category: $('#subcategory').val(),
            description: $('#description').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        }

        if (form_data.category !== '' && form_data.sub_category !== '') {
            $.ajax({
                url: "{{route('harbour_permit_category.store')}}",
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
</script>


@endsection