@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<style>
    h4 {
    font-size: 15px !important;
}
    </style>
    
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Approvals/</span>Cargo/Container</h4>
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
                            <h4>Cargo Details</h4>
                            <hr class="sky-blue">
                        </div>

                        <div class="col-6">
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Vehicle
                                        Status</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">FDFGD</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">CHA
                                        Name</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">DEMO</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Vessel
                                        No</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        V54354
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Vessel
                                        Name</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">VTEST</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">From
                                        Location</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">ERODE</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">To Location</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">CBE</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Filled Qty (In
                                        Tons)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">1 TONS</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Deliverd Qty (In
                                        Tons)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">1
                                        TONS</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Blanced Qty (In
                                        Tons)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">1 TONS</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Filled
                                        Qty (In
                                        Pkgs)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        500KGS
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">LDeliverd Qty (In
                                        Pkgs)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">100
                                        Pkgs</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Blanced
                                        Qty (In
                                        Pkgs)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">50</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Shipping Bill
                                        No</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">ShiP74747</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Shipping Bill
                                        Date</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">23-10-2024</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Total Qty (In
                                        Tons)</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">50</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">No of
                                        Packages</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">5</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Remark</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">demo</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Cargo</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">demo</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Consignee
                                        Name</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">PRABU</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Port of
                                        Origin/Destination</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">
                                        FDFGD
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Vehicle
                                        No</label>
                                    <span class="colon">:</span>
                                    <span class="value-col">vech65456</span>
                                </div>
                            </div>

                        </div>
                        <!-- </div> -->


                    </div>

                    <div class="row">

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
                                    <div class=" col-md-12 mb-2">
                                        <div class="label-container">
                                            <label class="form-label label-col" for="fullname">Designation</label>
                                            <span class="colon">:</span>
                                            <span class="value-col"></span>
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
                                                <option value="0">Pending</option>
                                                <option value="1">Approved</option>
                                                <option value="2">Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3" id="reject_reason" style="display:none;">
                                    <div class="label-container">
                                        <label class="form-label label-col">Reject Reason</label>
                                        <span class="colon">:</span>
                                        <span class="value-col"><textarea class="form-control value-col"
                                                style="width:300px; height:70px;" id="reject"></textarea>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("cargo_authentication")}}'">Cancel</button>
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