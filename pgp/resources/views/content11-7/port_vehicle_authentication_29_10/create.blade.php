@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Approvals / </span>Port Vehicle</h4>
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
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <form id="port_vehicle_form" method="put" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Port Vehicle Details</h5>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="vech_reg_no">Vehicle Number
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$portVehicle->vech_reg_no}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="vehicle_type">Vehicle Type</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$portVehicle->vehicle_type}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="owner_name">Owner's Namw</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$portVehicle->owner_name}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="contact_number">Contact Number</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$portVehicle->contact_number}}</span>
                                    </div>
                                </div>

                                <!-- <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="acc_year">
                                            Accadamic Year
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$portVehicle->acc_year}}
                                    </div>
                                </div> -->
                        </div>
                    </div>
                    <div class="card mb-4">
    <div class="card-body">
        <h5>Document Details</h5>
        <hr>
        @if($portVehicle_sub->count() > 0)
            <div class="card-datatable table-responsive">
                <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S No</th>
                            <th>Proof Type</th>
                            <th>Proof No</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($portVehicle_sub as $key => $sub)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $sub->certificate_type }}</td>
                                <td>{{ $sub->certificate_no }}</td>
                                <td>
    @php
        // Get the full path for the attached proof
        $filePath = 'public/uploads/port_vehicle_registration/' . $sub->attached_proof;
        $extension = pathinfo($sub->attached_proof, PATHINFO_EXTENSION);
    @endphp
    @if(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'pdf']))
        <a href="{{ asset($filePath) }}" download>
            <img src="{{ asset('public/assets/img/pdf.jpg') }}" style="width: 10%; height: auto; display: block;">
        </a>
    @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
        <a href="#" onclick="openImage('{{ asset($filePath) }}')">
            <img src="{{ asset($filePath) }}" style="width: 10%; height: auto; display: block;">
        </a>
    @else
        <p>File type not supported for preview or download.</p>
    @endif
</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No documents available.</p>
        @endif
    </div>
</div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5>Authentication User Details</h5>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">Authenticated By
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{session('user_name')}}</span>
                                    </div>
                                    <input type="hidden" name="user_id" id="user_id" value="{{ session('user_id') }}">
                                </div>
                                
                               
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="col-md-12 mb-2 mt-3">
                                <div class="label-container">
                                    <label class="form-label label-col" for="fitness-certificate">
                                        Status</label>
                                    <span class="colon">:</span>
                                    <!-- <span class="value-col"></span> -->
                                    <div class="col-5"><select class="form-select value-col" id="approve" name="approve"
                                            onchange="get_reject_reason()">
                                            <!-- <option value="selected">Change Status</option> -->
                                            <option value="0" {{$portVehicle->status == 0 ? 'selected' : '' }} selected>
                                                Pending</option>
                                            <option value="1" {{$portVehicle->status == 1 ? 'selected' : '' }}>
                                                Approved</option>
                                            <option value="2" {{$portVehicle->status == 2 ? 'selected' : '' }}>
                                                Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2" style="display:none" id="reject-reason-div">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Reject Reason
                                    </label>
                                    <span class="colon">:</span>
                                    <div class="col-5">
                                        <textarea type="text" class="form-control" id="reject_reason" name="reject_reason"
                                            value="{{ $portVehicle->reject_reason }}"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                </div>
            </div>
            

                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("port_vehicle_authentication")}}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    get_reject_reason();



function openImage(url) {
    // Open the image in a new external window
    window.open(url, '_blank', 'width=800,height=600');
}
function get_reject_reason() {
   
    var approve = $("#approve").val();
    let rejectReasonDiv = document.getElementById('reject-reason-div');

    if (approve == "2") {

        rejectReasonDiv.style.display = "block";  // Show the Reject Reason div
    } else {

        rejectReasonDiv.style.display = "none";
        $("#reject_reason").val('');  // Hide the Reject Reason div
    }

}
    

    function submitForm(event) {
        event.preventDefault();



        let formData = {
            approve: $('#approve').val(),
            reject_reason: $('#reject_reason').val(),
            user_id: $('#user_id').val(),
            _token: '{{ csrf_token() }}',
            _method:'PUT'           
        };


        let url = '{{ route("portVehicle.update", $portVehicle->id) }}';
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function (response) {

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Updated Successfully!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = "{{ route('port_vehicle_authentication') }}";
                });
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.usertype) {
                    $('#usertypeError').text(errors.usertype[0]);
                }
            }
        });

        return false;
    }
   

    function imageViews(fileName) {
    // console.log("File Name: ", fileName); // Check the file name
    var fileUrl = ../../harbour/public/uploads/employee_registration/${fileName};
    // console.log("File URL: ", fileUrl); // Check the constructed URL

    var a = document.createElement('a');
    a.href = fileUrl;
    a.download = fileName;

    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

@endsection