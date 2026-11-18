@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Approvals / </span>Firms/Agent</h4>
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


@php
$get_firm_type = get_company();
@endphp
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <form id="firmauth_form" method="post" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Firm User Details</h5>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fullname">Types of Agent
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">
                                            @foreach ($get_firm_type as $get_firm )
                                            @if($get_firm->unique_id == $firmauth->firms)
                                            {{$get_firm->company_name}}
                                            @endif
                                            @endforeach

                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="email">Reg NO</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$firmauth->company_reg_no}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="mobile">Reg Date</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$firmauth->created_at}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="aadhaar">Name</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$firmauth->name}}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">
                                            Address1
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$firmauth->address_1}}
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">
                                            GST NO
                                        </label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{$firmauth->gstin}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance">Mobile No
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$firmauth->mobile}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-from">Email id
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$firmauth->email}}</span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Contact Person
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$firmauth->contact_person}}</span>
                                </div>
                            </div>


                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Address2
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$firmauth->address_2}}</span>
                                </div>
                            </div>



                            <div class="col-md-12 mb-2">
                                <div class="label-container">
                                    <label class="form-label label-col" for="insurance-valid-to">Fax No
                                    </label>
                                    <span class="colon">:</span>
                                    <span class="value-col">{{$firmauth->fax_no}}</span>
                                </div>
                            </div>



                        </div>
                    </div>



                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Document Details</h5>
                    <hr>
                    @if($firmauth_sub->count() > 0)
                    <!-- <div class="col-md-12 mb-2"> -->
                    <!-- <div class="label-container"> -->
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

                            @foreach($firmauth_sub as $key => $sub)
                            <td>{{$key + 1}}</td>
                            <td>{{$sub->proof_type}}</td>
                            <td>{{$sub->aadhar_licence}}</td>
                            <td>
                                @php
                                // Explode the file name to get the extension
                                $extension = pathinfo($sub->attached_proof, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'pdf']))
                                <!-- For documents, provide a download link -->
                                <a href="{{ asset('public/uploads/firms_registration/aadhaar/' . $sub->attached_proof) }}"
                                    download>
                                    <image src="{{ asset('public/assets/img/pdf.jpg') }}"
                                        style="width: 10%; height: auto; display: block; "></image>
                                </a>
                                @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <a href="#"
                                    onclick="openImage('{{ asset('public/uploads/firms_registration/aadhaar/' . $sub->attached_proof) }}')">
                                    <image
                                        src="{{ asset('public/uploads/firms_registration/aadhaar/' . $sub->attached_proof) }}"
                                        style="width: 10%; height: auto; display: block; ">
                                    </image>
                                </a>
                                @else
                                <p>File type not supported for preview or download.</p>
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- </div> -->
                    <!-- </div> -->
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
                                        <label class="form-label label-col" for="insurance-valid-to">Authenticated
                                            By</label>
                                        <span class="colon">:</span>
                                        <span class="value-col">{{ session('user_name') }}</span>
                                        <input type="hidden" id="approved_id" value="{{ session('user_id') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2 mt-3">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="fitness-certificate">Status</label>
                                        <span class="colon">:</span>
                                        <div class="col-5">
                                            <select class="form-select value-col" id="approve" name="approve"
                                                onchange="get_reject_reason()">
                                                <option value="0" {{ $firmauth->status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="1" {{ $firmauth->status == 1 ? 'selected' : '' }}>
                                                    Approved</option>
                                                <option value="2" {{ $firmauth->status == 2 ? 'selected' : '' }}>
                                                    Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2" style="display:none" id="reject-reason-div">
                                    <div class="label-container">
                                        <label class="form-label label-col" for="insurance-valid-to">Reject
                                            Reason</label>
                                        <span class="colon">:</span>
                                        <div class="col-6">
                                            <textarea class="form-control" id="reject_reason"
                                                name="reject_reason">{{ $firmauth->reject_reason }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route("firm_authentication")}}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
get_reject_reason();


function openImage(url) {
    // Open the image in a new external window
    window.open(url, '_blank', 'width=800,height=600');
}

function get_reject_reason() {
    var approve = $("#approve").val();
    let rejectReasonDiv = document.getElementById('reject-reason-div');

    if (approve == "2") {

        rejectReasonDiv.style.display = "block"; // Show the Reject Reason div
    } else {

        rejectReasonDiv.style.display = "none";
        $("#reject_reason").val(''); // Hide the Reject Reason div
    }

}

function submitForm(event) {
    event.preventDefault();



    let formData = {
        approve: $('#approve').val(),
        reject_reason: $('#reject_reason').val(),
        approved_by: $('#approved_id').val(),

        _token: '{{ csrf_token() }}',
        _method: 'PUT'
    };


    let url = '{{ route("firmauth.update", $firmauth->id) }}';
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Updated Successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('firm_authentication') }}";
            });
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors.usertype) {
                $('#usertypeError').text(errors.usertype[0]);
            }
        }
    });

    return false;
}


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
</script>

@endsection