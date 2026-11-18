@extends('layouts/contentNavbarLayout')

@section('title', 'Taluk Form')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4>
            <span class="text-muted fw-light">Entry Form /</span> Taluk {{ isset($talukName) ? 'Edit' : 'Creation' }}
        </h4>
    </div>
</div>

@php


$zone_name_options = get_zone();
$district_name_options = get_district();
@endphp

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="talukForm" method="post" onsubmit="return submitForm(event);">
                    @csrf
                    @if(isset($talukName))
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $talukName->id }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="zone_name">Zone Name</label>
                        <select class="form-select" id="zone_id" name="zone_id">
                            <option value="selected">Select Zone</option>
                            @foreach($zone_name_options as $zone)
                            <option value="{{ $zone->unique_id }}"
                                {{ isset($talukName) && $talukName->zone_name == $zone->unique_id ? 'selected' : '' }}>
                                {{ $zone->zone }}
                            </option>
                            @endforeach
                        </select>
                        <span id="zone_nameError" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="district_name">District Name</label>
                        <select class="form-select" id="district_name" name="district_name">
                            <option value="selected">Select District</option>

                            @foreach($district_name_options as $district)
                            <option value="{{$district->unique_id}}"
                                {{ isset($talukName) && $talukName->district_name == $district->unique_id ? 'selected' :''}}>
                                {{$district->district}}
                            </option>
                            @endforeach
                        </select>
                        <!-- <input type=" text" class="form-control" id="district_name"
                                name="district_name" value="{{ $talukName->district_name ?? '' }}" /> -->
                        <span id="district_nameError" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="taluk_name">Taluk Name</label>
                        <input type="text" class="form-control" id="taluk_name" name="taluk_name"
                            value="{{ $talukName->taluk_name ?? '' }}" />
                        <span id="taluk_nameError" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="remark">Remarks</label>
                        <textarea class="form-control" id="remark"
                            name="remark">{{ $talukName->remark ?? '' }}</textarea>
                        <span id="remarkError" class="text-danger"></span>
                    </div>

                    <div class="btn-float">
                        <button class="btn btn-danger" type="button"
                            onclick="window.location.href='{{ route('taluk_creation.index') }}'">Cancel</button>
                        <button type="submit"
                            class="btn btn-primary">{{ isset($talukName) ? 'Update' : 'Submit' }}</button>
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
$('select#zone_id').on('change', function() {
    var zoneId = $(this).val();

    if (!zoneId) {
        $('#district_name').empty().append('<option value="">Select District</option>');
        return;
    }

    $.ajax({
        type: "GET",
        url: "{{ route('fetch_districts') }}",
        data: {
            'zone_id': zoneId
        },
        cache: false,
        success: function(response) {
            if (response && response.districts && response.districts.length > 0) {
                var $districtDropdown = $('#district_name');
                $districtDropdown.empty(); // Clear existing options
                $districtDropdown.append(
                    '<option value="">Select District</option>'); // Default option

                $.each(response.districts, function(index, district) {
                    $districtDropdown.append(
                        `<option value="${district.unique_id}">${district.district}</option>`
                    );
                });
                @if(isset($talukName))
                $districtDropdown.val('{{ $talukName->district_name }}');
                @endif
            } else {
                $('#district_name').empty().append('<option value="">Select District</option>');
            }
        },
        error: function(xhr) {
            console.error('Error fetching districts:', xhr.responseText);
        }
    });
});
// $(document).ready(function() {

//     $('#zone_id').on('change', function() {
//         var zone_id = $('#zone_name').val();

//         alert(zone_id);
//         console.log(zone_id);

//         $.ajax({
//             url: "{{route('fetch_districts')}}",
//             type: "GET",
//             data: {
//                 'zone_id': zone_id
//             },
//             success: function(response) {
//                 console.log(response);
//                 $('#district_name').empty();
//                 $('#district_name')
//                     .append('<option value="">Select District</option>');
//                 $.each(
//                     response.district,
//                     function(index, district) {
//                         $('#district_name').append('<option value="' + district
//                             .unique_id +
//                             '">' + district.district + '</option> ')

//                     });

//             }

//         })



//     });


// });

function submitForm(event) {
    event.preventDefault();

    let formData = {
        zone_name: $('#zone_id').val(),
        district_name: $('#district_name').val(),
        taluk_name: $('#taluk_name').val(),
        remark: $('#remark').val(),
        _token: '{{ csrf_token() }}',
        _method: "{{ isset($talukName) ? 'PUT' : 'POST' }}"
    };

    let url =
        "{{ isset($talukName) ? route('taluk_creation.update', $talukName->id) : route('taluk_creation.store') }}";

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            // Display success message using SweetAlert2
            Swal.fire({
                title: 'Success!',
                text: 'Taluk ' + (formData._method === 'POST' ? 'created' : 'updated') +
                    ' successfully!',
                icon: 'success',
                confirmButtonText: 'OK',
                timer: 1000, // Automatically close after 1 seconds
                showConfirmButton: false // Disable OK button
            }).then(() => {
                // Redirect after the alert is closed
                window.location.href = "{{ url('/taluk_creation') }}";
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                $(`#${field}Error`).text(errors[field][0]);
            }
        }
    });

    return false;
}



// function validateForm() {
// let isValid = true;

// let taluk_nameInput = document.getElementById('taluk_name');
// let district_nameInput = document.getElementById('district_name');
// let zone_nameInput = document.getElementById('zone_name');
// $('.text-danger').text('');

// if ($('#zone_name').val().trim() === '') {
// $('#zone_nameError').text('Zone Name is required.');
// isValid = false;
// }

// if ($('#district_name').val().trim() === '') {
// $('#district_nameError').text('District Name is required.');
// isValid = false;
// } else if (!/^[a-zA-Z]+$/.test(district_nameInput.value.trim())) {
// district_nameError.textContent = 'District Name must contain only alphabet characters.';
// isValid = false;
// }

// if ($('#taluk_name').val().trim() === '') {
// $('#taluk_nameError').text('Taluk Name is required.');
// isValid = false;
// } else if (!/^[a-zA-Z]+$/.test(taluk_nameInput.value.trim())) {
// taluk_nameError.textContent = 'Taluk Name must contain only alphabet characters.';
// isValid = false;
// }

// if ($('#remark').val().trim() === '') {
// $('#remarkError').text('Remarks are required.');
// isValid = false;
// }

// return isValid;
// }
</script>
@endsection