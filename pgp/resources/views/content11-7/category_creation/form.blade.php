@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Category</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="category_type">Category Type</label>
                            <!-- <select class="form-select" name="category_type" id="category_type">
                                <option value="Break Fast">Break Fast</option>
                                <option value="Lunch">Lunch</option>
                                <option value="School In">School In</option>
                                <option value="School Out">School Out</option>
                            </select> -->
                            <input type="text" class="form-control santizeinput" id="category_type"
                                name="category_type" />
                        </div>

                        <div class=" col-md-4 mb-3">
                            <label class="form-label" for="description">Remarks</label>
                            <textarea id="description" class="form-control santizeinput" name="description"></textarea>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/category_list')}}'">Cancel</button>

                        <button type="submit" class="btn btn-primary">Submit</button>
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
    $(document).ready(function () {

        $('.santizeinput').on('input', function () {
            let value = $(this).val();
            let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _]/g, ''); // Remove special characters
            $(this).val(sanitizedValue);
        });
        $('.santizepassword').on('input', function () {
            let value = $(this).val();
            let sanitizedValue = value.replace(/[^a-zA-Z0-9 - _ @]/g, ''); // Remove special characters
            $(this).val(sanitizedValue);
        });


        $('#category_form').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission


            var formData = {

                category_type: $('#category_type').val(),
                description: $('#description').val(),
                created_at: new Date().toISOString(), // Example: current date-time
                updated_at: new Date().toISOString(),
                _token: '{{ csrf_token() }}'
            };

            // console.log('Form Data:', formData);

            if ((formData.category_type != '')) {

                $.ajax({
                    url: "{{route('category.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // alert();
                        const redirectUrl = '{{ route("category_list") }}';
                        sweetalert('create', redirectUrl);

                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {

                            sweetalert('already');
                            // Swal.fire({
                            //     icon: 'info',
                            //     title: 'Already Exist!',
                            //     showConfirmButton: true,
                            //     timer: 1500,
                            //     timerProgressBar: true
                            // });
                        }

                    }
                });
            } else {
                sweetalert("form_alert");
            }
        })
    });


    //                 function appendToTable(category) {
    //                     var newRow = `<tr>
    //     <td>${category.id}</td>
    //     <td>${category.category_type}</td>
    //     <td>${category.description}</td>
    // </tr>`;
    //                     $('#categoryTableBody').append(newRow);
    //                 }
</script>

@endsection