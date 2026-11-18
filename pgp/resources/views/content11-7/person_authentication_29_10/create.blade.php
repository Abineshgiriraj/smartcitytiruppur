@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Approvals/</span>Person/Driver</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}

.label-container {
    display: flex;
    align-items: center;
    /* Center vertically */
    width: 100%;
    /* Full width for proper alignment */
}

.label-col {
    width: 200px;
    /* Fixed width for labels */
    margin-right: 10px;
    /* Space between label and colon */
}

.colon {
    width: 20px;
    /* Fixed width for colon */
    text-align: center;
    /* Center the colon */
}

.value-col {
    flex: 3;
    /* Take the remaining space */
    text-align: left;
    /* Align the value to the left */
}

.sky-blue {
    border: none;
    /* Remove default border */
    border-top: 2px solid skyblue;
    /* Set the sky blue color */
    margin-bottom: 4px 0;
    /* Adjust margin as needed */
}
</style>

@php
$userId = Session::get('user_id');


$get_designations =get_designation();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row">
                        <div class="row">
                            <h4>Person Details</h4>
                            <hr class="sky-blue">
                        </div>

                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fullname">Full Name</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->full_name}}</span>
                                    </div>
                                </div>

                                <input type="hidden" id="main_unique_id" value="{{$person_registration->unique_id}}">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="email">Company Name</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->company_name}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="mobile">Reg Date</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->register_date}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="aadhaar">Aadhaar No</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->aadhaar_no}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">Country</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->country}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">Mobile No</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$person_registration->mobile}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Register No</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$person_registration->register_no}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Designation</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        @foreach ($get_designations as $designation )
                                        <!-- {{ $designation->unique_id == $person_registration->designation}} -->
                                        @if ($designation->unique_id == $person_registration->designation)
                                        {{$designation->designation}}
                                        @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Licence Expiry
                                        Date</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$person_registration->licence_validity_date}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Nationality</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$person_registration->nationality}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Email ID</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$person_registration->email_id}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Blood Group</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$person_registration->blood_group}}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row">
                            <h4>Document Details</h4>
                            <hr class="sky-blue">
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div id="dataTableContainer" class="table-responsive mt-4">
                                        <table id="sublist" class="table table-striped table-bordered"
                                            style="width: 100%;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Type of ID Proof</th>
                                                    <th>Aadhar No./Licence No.</th>
                                                    <th>Document</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($person_sub_reg && $person_sub_reg->count() > 0)
                                                @foreach ($person_sub_reg as $sublist)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $sublist->proof_type }}</td>
                                                    <td>{{ $sublist->aadhar_licence }}</td>
                                                    <td>
                                                        @php
                                                        // Determine the file extension
                                                        $fileExtension = strtolower(pathinfo($sublist->attached_proof,
                                                        PATHINFO_EXTENSION));
                                                        // Set the base directory based on proof_type
                                                        $baseDirectory = $sublist->proof_type === 'aadhaar'
                                                        ? 'public/uploads/person_registration/aadhaar/'
                                                        : 'public/uploads/person_registration/licence/';
                                                        // Construct the full file path
                                                        $filePath = asset($baseDirectory . $sublist->attached_proof);
                                                        @endphp

                                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png']))
                                                        <a href="{{ $filePath }}" download>
                                                            <img src="{{ $filePath }}"
                                                                alt="{{ $sublist->attached_proof }}"
                                                                style="max-width: 100px; max-height: 100px; margin-left: 10px;" />
                                                        </a>
                                                        @elseif ($fileExtension === 'pdf')
                                                        <a href="{{ $filePath }}" download>
                                                            <img src="{{ asset('public/uploads/dwn.png') }}"
                                                                alt="PDF Icon"
                                                                style="max-width: 100px; max-height: 100px; margin-left: 10px;" />
                                                        </a>
                                                        @elseif ($fileExtension === 'docx')
                                                        <a href="{{ $filePath }}" download>
                                                            <img src="{{ asset('path/to/docx-icon.png') }}"
                                                                alt="DOCX Icon"
                                                                style="max-width: 100px; max-height: 100px; margin-left: 10px;" />
                                                            <span
                                                                style="margin-left: 10px;">{{ $sublist->attached_proof }}</span>
                                                        </a>
                                                        @else
                                                        <a href="javascript:void(0);"
                                                            onclick="imageViews('{{ $sublist->attached_proof }}', '{{ $sublist->proof_type }}')">
                                                            {{ $sublist->attached_proof }}
                                                        </a>
                                                        <span style="margin-left: 10px;">(File type:
                                                            {{ strtoupper($fileExtension) }})</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="4" class="text-center">No documents found.</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <h4>Approval Officer Details</h4>
                                <hr class="sky-blue">
                            </div>

                            <div class="col-6">
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <div class="label-container">
                                            <label class="form-label label-col" for="fullname">User Name</label>
                                            <span class="colon">:</span>
                                            <span class="value-col">{{ session('user_name') }}</span>
                                            <input type="hidden" id="approve_id" value="{{session('user_id')}}">
                                        </div>
                                    </div>

                                </div>



                                <div class="col-md-12 mb-2 mt-3">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fitness-certificate">Status</label>
                                        <span class="colon">:</span>
                                        <div class="col-5">
                                            <select class="form-select value-col" id="approve" name="approve"
                                                onchange="status_change()">
                                                <option value="selected">Change Status</option>
                                                <option value="0"
                                                    {{$person_registration->status == '0' ? 'selected'  : '' }}>Pending
                                                </option>
                                                <option value="1"
                                                    {{$person_registration->status == '1' ? 'selected'  : '' }}>Approved
                                                </option>
                                                <option value="2"
                                                    {{$person_registration->status == '2' ? 'selected'  : '' }}>Rejected
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3" id="reject_reason" style="display:none;">
                                    <div class="label-container">
                                        <label class="form-label label-col">Reject Reason</label>
                                        <span class="colon">:</span>
                                        <span class="value-col"><textarea class="form-control value-col"
                                                style="width:300px; height:70px;"
                                                id="reject">{{$person_registration->reject_reason}}</textarea>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("person_authentication")}}'">Cancel</button>
                            <button type="button" onclick="status_upload()" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
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
function updateLabel() {
    const transactionType = document.getElementById('transaction_type').value;
    const dynamicLabel = document.getElementById('dynamic_label');
    const dynamicLabel_1 = document.getElementById('dynamic_label_1');

    if (transactionType === 'transaction_type_1') {
        dynamicLabel.textContent = "Export No (IA/EA No)";
        dynamicLabel_1.textContent = 'Export Name';
    } else {
        dynamicLabel.textContent = "Import No (IA/EA No)";
        dynamicLabel_1.textContent = 'Import Name';
    }
}


function imageViews(file_name, proof_type) {
    // Split the file names by commas (in case there are multiple)
    var fileNames = file_name.split(',');

    // Base directory wher your files are stored
    var aadhaarDirectory = 'uploads/person_registration/aadhaar/';
    var licenceDirectory = 'uploads/person_registration/licence/';


    fileNames.forEach(function(fileName) {
        // Get the file extension
        var cFileName = fileName.split('.');
        var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
        var filePath = `${baseDirectory}${fileName}`; // Construct the full path



        var baseDirectory;

        if (proof_type == 'aadhaar') {
            baseDirectory = aadhaarDirectory;
        } else if (proof_type == 'driving_licence') {
            baseDirectory = licenceDirectory;

        }

        var filePath = `${baseDirectory}${fileName}`;

        if (fileExtension === 'jpg' || fileExtension === 'png' || fileExtension === 'jpeg') {
            // Open image in a new tab
            window.open(filePath, '_blank');
        } else if (fileExtension === 'pdf') {

            window.open(filePath, '_blank');
            // Trigger download for PDF
            // const link = document.createElement('a');
            // link.href = filePath;
            // link.download = fileName; // Trigger download
            // link.click();
        } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
            // Trigger Excel download
            const link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;
            link.click();
        } else if (fileExtension === 'txt' || fileExtension === 'docx' || fileExtension === 'doc') {
            // Trigger download for text or Word files
            const link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;
            link.click();
        }
    });
}


function status_change() {

    var status = $('#approve').val();


    if (status === '2') {
        $('#reject_reason').show();
    } else {
        $('#reject_reason').hide(); // Hide it otherwise
    }
}

function status_upload() {


    // alert('statusuploasd');
    var status = $('#approve').val();
    var approved_by = $('#approve_id').val();

    var reject_reason = $('#reject').val();
    var main_unique_id = $('#main_unique_id').val();

    var data = {
        status: status,
        approved_by: approved_by,
        reject_reason: reject_reason,
        main_unique_id: main_unique_id,
        _token: "{{csrf_token()}}"
    }


    $.ajax({
        url: "{{route('person_authentication.status')}}",
        type: "POST",
        data: data,
        success: function(response) {
            const redirecturl = "{{route('person_authentication')}}";
            sweetalert("create", redirecturl);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });

}
</script>

@endsection