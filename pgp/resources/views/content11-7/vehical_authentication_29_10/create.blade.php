@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Approvals/</span>Vehicel/Equipments</h4>
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
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf



                    <div class="row">
                        <div class="row">
                            <h4>Vehicle Details</h4>
                            <hr class="sky-blue">
                        </div>

                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fullname">Vehicle No</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$vehicle_reg->vech_reg_no}}</span>
                                    </div>
                                </div>


                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="mobile">Owner Name</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$vehicle_reg->owner_name}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-6">
                            <div class="row">
                            <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="email">Type of Vehicle</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$vehicle_reg->status}}</span>
                                    </div>
                                    </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="email">Vehicle Category</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$vehicle_reg->vehicle_category}}</span>
                                    </div>
                                </div>



                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="aadhaar">Contact Number</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$vehicle_reg->contact_number}}</span>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>


                    <div class="row mt-3">
                        <div class="row">
                            <h4>Document Details</h4>
                            <hr class="sky-blue">
                        </div>
                        <input type="hidden" id="main_unique_id" value="{{$vehicle_reg->screenUniqueId}}">

                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <div id="dataTableContainer" class="table-responsive mt-4">

                                        <table id="sublist" class="table table-striped table-bordered"
                                            style="width: 100%;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Certificate No</th>
                                                    <th>Certificate Type</th>
                                                    <th>Valid Upto</th>
                                                    <th>Document</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($vehicle_sub_reg as $item )

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{$item ->certificate_no}} </td>
                                                    <td>{{$item ->certificate_type}}</td>
                                                    <td>{{$item ->valid_up_to}}</td>

                                                    <td>
                                                        <a onclick="imageViews('{{$item->attached_proof}}')"><img
                                                                src='/harbour/public/uploads/download.png' width="40px"
                                                                height="40px"></a>
                                                    </td>
                                                    <!-- <td>{{$item ->attached_proof}}</td> -->
                                                </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    <div class="row mt-4">
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
                                        <span class="value-col">{{session('user_name') }}</span>
                                        <input type="hidden" id="approved_id" value="{{session('user_id')}}">
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
                                            <option value="1"
                                                {{$vehicle_reg->approval_status == '1' ? 'selected' : ''}}>
                                                Approved
                                            </option>
                                            <option value="2"
                                                {{$vehicle_reg->approval_status == '2' ? 'selected' : ''}}>
                                                Rejected
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
                                            id="reason_rejected">{{$vehicle_reg->reject_reason}}</textarea>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("vehicle_authentication")}}'">Cancel</button>
                            <button type="button" onclick="status_approval()" class="btn btn-primary">Submit</button>
                        </div>
                    </div>


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

function imageViews(fileName) {
    //   alert(fileName);
    // Construct the URL for the file
    var fileUrl =
        `/harbour/public/uploads/vehicle_registration/{{$item->attached_proof}}`; // Update this path as needed

    // Create a temporary anchor element
    var a = document.createElement('a');
    a.href = fileUrl;
    a.download = fileName; // Set the file name for download

    // Append the anchor to the body (not necessary, but helps in some cases)
    document.body.appendChild(a);

    // Simulate click on the anchor
    a.click();

    // Clean up by removing the anchor
    document.body.removeChild(a);
}

function status_change() {
    var status = $('#approve').val();

    if (status === '2') {
        $('#reject_reason').show();
    } else {
        $('#reject_reason').hide();

    }
}



function status_approval() {

    var status = $('#approve').val();
    var reject_reason = $('#reason_rejected').val();
    var approved_by = $('#approved_id').val();

    var screen_unique_id = $('#main_unique_id').val();

    var data = {
        status: status,
        reject_reason: reject_reason,
        approved_by: approved_by,
        screen_unique_id: screen_unique_id,
        _token: "{{csrf_token()}}"

    }
    $.ajax({
        url: "{{route('vehical_authentication.approval_status')}}",
        data: data,
        type: "POST",

        success: function(response) {
            const redirecturl = "{{route('vehicle_authentication')}}";

            sweetalert("create", redirecturl);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}
</script>

@endsection