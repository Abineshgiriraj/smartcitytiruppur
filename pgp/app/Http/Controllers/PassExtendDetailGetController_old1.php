@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">HEP Extension/</span>Pass Extend</h4>
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

.headname {
    color: #5859ae;
}

.hrcode {
        border: 1px solid #5859ae;
        /* Customize the color and thickness */
        margin: 20px 0;
        /* Space around the hr */
    }
</style>
<div class="row">
    <div class="col-xl-12">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf

                    <!-- Search Section -->
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-4 text-md-end text-center">
                            <label for="rfidInput" class="form-label me-2">Hep Card No</label>
                        </div>
                        <div class="col-md-5 text-center">
                            <input type="text" class="form-control" placeholder="Search RFID" id="rfidInput" name="rfidInput">
                            
                        </div>
                        <div class="col-md-3 text-md-start text-center mt-md-0 mt-2">
                            <button type="button" class="btn btn-primary" onclick="RfidSearch();loadTableData()">Go</button>
                        </div>
                        <div class="col-md-12 text-center md-4">
                        <span id="rfidInputerror" class="text-center md-2" style="color: red; display: none; width: 100%; text-align: center; display: none;">Invalid Hep Rfid number.</span>
                        </div>
                    </div>

                    <hr class="hrcode">
                    <!-- Details Section -->
                    <div id="details-tab1" class="card-body" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 headname" >Company Details</h5>
                                <div class="mb-2">
                                    <label class="form-label">Company Name:</label>
                                    <b><span class="value-col" id="firm_name"></span></b>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Address:</label>
                                    <b><span class="value-col" id="address"></span></b>
                                </div>
                            </div>

                            <div class="col-md-6" id="person_details" style="display: none;">
                                <h5 class="mb-3 headname">Person Details</h5>
                                <div class="mb-2">
                                    <label class="form-label">Name:</label>
                                    <b><span class="value-col" id="person_name"></span></b>
                                    <input type="hidden" id="proof_expiry_date_input" name="proof_expiry_date_input"/>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Aadhar No:</label>
                                    <b><span class="value-col" id="aadhar_no"></span></b>
                                    <input type="hidden" name="passType" id="passType">
                                    <input type="hidden" name="persontype" id="persontype">
                                </div>
                            </div>

                            <div class="col-md-6" id="vehicle_details" style="display: none;">
                                <h5 class="mb-3 headname">Vehicle Details</h5>
                                <div class="mb-2">
                                    <label class="form-label">Vehicle No:</label>
                                    <b><span class="value-col" id="vech_no"></span></b>
                                    <input type="hidden" name="vectype" id="vectype">
                                    <input type="hidden" name="visitorType" id="visitorType">
                                    <!-- <label class="col-sm-5 col-form-label" id="proof_expiry_date"
                                                    style="font-weight: bold;"></label> -->
                                  
                                    <input type="hidden" name="ins_valid_up_to_input" id="ins_valid_up_to_input">
                                    <input type="hidden" name="fc_valid_up_to_input" id="fc_valid_up_to_input">
                                    <input type="hidden" name="puc_valid_up_to_input" id="puc_valid_up_to_input">

                                </div>
                                <!-- <div class="mb-2">
                                    <label class="form-label">Driver License No:</label>
                                    <b><span class="value-col" id="license_no"></span></b>
                                </div> -->
                                <div class="mb-2">
                                    <label class="form-label">Owner Name:</label>
                                    <b><span class="value-col" id="owner_name"></span></b>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Expiry Date:</label>
                                    <b><span class="value-col" id="expiry_date"></span></b>
                                </div>
                            </div>
                        </div>

                        <hr class="hrcode">
                        <!-- Pass Details Section -->
                        <h5 class="mb-3 headname">Pass Details</h5>
                        <div class="row">
                            <!-- Left Column -->
                            <div class="row mb-3" >
                                <div class="col-md-2"> 
                                    <label class="form-label">Pass No:</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" name="pass_id_input" id="pass_id_input">
                                    <b><span id="pass_id"></span></b>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Access Zone:</label>
                                </div>
                                <div class="col-md-2">
                                <b><span id="access_zonelabel"></span></b>
                                <input type="hidden" name="access_zone"  id="access_zone">
                                </div>
                                <div class="col-md-2">
                                <label class="form-label">Access Area:</label>
                                </div>
                                <div class="col-md-2">
                                <b><span id="access_arealabel"></span></b>
                                <input type="hidden" name="access_area"  id="access_area">
                                     <!-- Add other pass fields as required -->
                            </div>
                            <!-- Right Column -->
                            <div class="row mb-3" >
                                <div class="col-md-2">
                                    <label class="form-label">From Date:</label>
                                </div>
                                <div class="col-md-3">
                                <b><span id="from_date"></span></b>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To Date:</label>
                                </div>
                                <div class="col-md-3">
                                <b><span id="to_date"></span></b>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Period:</label>
                                </div>
                                <div class="col-md-1">
                                <b><span id="period"></span></b>
                                </div>
                            </div>
                            <div class="col-md-2">
                                    <label class="form-label">Amount:</label>
                                </div>
                                <div class="col-md-2">
                                <b><span id="amount"></span></b>
                                </div>
                            <hr class="hrcode">
                            <h5 class="mb-3 headname" >Pass Extension</h5>
                                <div class="row mb-3" >
                                    <!-- Left: Pass Type | Right: Extend Period -->
                                    <div class="col-md-2">
                                        <label class="form-label">Extend Pass Type:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="extend_pass_type" id="extend_pass_type" onchange="get_vec_pass();calculateToDate(),get_amount();">
                                            <option value="">Select</option>
                                            <option value="day">Day</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Extend Period:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="extend_period" name="extend_period" class="form-control" value='1' oninput="periodAmt();calculateamount();">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">From Date:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="datetime-local" name="old_from_date" id="old_from_date" class="form-control" oninput="calculateToDate1();" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" id="from_date_display" class="form-control" />
                                    </div>
                                    
                                </div>
                               

                                <div class="row mb-3" >
                                    <div class="col-md-2">
                                        <label class="form-label">Extend To Date:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="datetime-local" id="extend_to_date" name="extend_to_date" class="form-control">
                                        <input type="hidden" id="date_display" name="date_display">
                                    </div>
                                    <!-- Left: Extend Upload | Right: Extend Amount -->
                                    <div class="col-md-1">
                                        <label class="form-label">Extend Upload:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="file" name="extend_file_upload" id="extend_file_upload" class="form-control">
                                    </div>
                                
                                    <div class="col-md-1">
                                        <label class="form-label">Extend Amount:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="extend_gross_amount" name="extend_gross_amount" class="form-control" oninput="calculateamount()" readonly>
                                        <input type="hidden" class="form-control" id="amount_day" name="amount_day" readonly />
                                        <label  class="col-form-label" id="extend_gross_amountlabel"
                                            name="extend_gross_amountlabel"></label>
                                        <!--  -->

                                    </div>
                                </div>
                                <div class="row  justify-content-end no-margin" >
                                    <div class="col-sm-1 text-end">
                                        <label for="sgst" class="col-form-label">SGST(9%)</label>
                                    </div>
                                    <div class="col-sm-1 text-end">
                                        <label class="col-form-label" id="sgstLabel"></label>
                                        <input type="hidden" class="form-control" id="extend_sgst" name="extend_sgst" />
                                    </div>

                                    <div class="col-sm-1 text-end">
                                        <label for="cgst" class="col-form-label">CGST(9%)</label>
                                    </div>
                                    <div class="col-sm-1 text-end">
                                        <label class="col-form-label" id="cgstLabel"></label>
                                        <input type="hidden" class="form-control" id="extend_cgst" name="extend_cgst" />
                                    </div>
                                </div>
                                <div class="row  justify-content-end" >
                                    <div class="col-sm-2 text-end">
                                        <label for="extend_gross_amount"  class="col-form-label" style="display:none">Total Extend Gross
                                            Amount</label>
                                    </div>

                                    <div class="col-sm-2 text-end" style="display:none">
                                        <label class="col-form-label" id="extend_net_amountlabel"
                                            name="extend_net_amountlabel"></label>
                                        <input type="hidden" class="form-control" id="extend_net_amount" name="extend_net_amount"  />
                                        <!-- <input type="hidden" class="form-control" id="extend_net_amount" name="extend_net_amount" /> -->

                                        <label  class="col-form-label" id="extend_tax_amountlabel"
                                        name="extend_tax_amountlabel"></label>
                                        <input type="hidden" class="form-control" id="extend_tax_amount" name="extend_tax_amount" />
                                    </div>
                                </div>
                            </div>
                           
                            <div class="row mb-3 mt-2 justify-content-end" >
                                <div class="col-sm-3">
                                    <label for=" colFormLabelSm" class="col-form-label"><b>Total Pass Amount</b></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class=" col-form-label" id="pass_amount_label" ></label>
                                    <input type="hidden" class="form-control" id="pass_amount" name="pass_amount" />
                                    <label class="col-form-label" id="with_tax_amount_label"></label>
                                    <input type="hidden" class="form-control" id="with_tax_pass_amount" name="with_tax_pass_amount" />
                                </div>
                            </div>
                           
                            <div class="justify-content-center submit_sublist">
                                        <button id="openTableButton submit_sublist" onclick="sublist_add(event)"
                                            class="btn btn-primary" style="float: right;">ADD</button>
                            </div> 
                            <br><br>
                            <div id="dataTableContainer" class="table-responsive ">
                                <table id="sublist" class="table table-striped table-bordered" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                        <th>S.No</th>
                                        <th>Pass Type.</th>
                                        <th>Extend Period</th>
                                        <th>Extend To Date</th>
                                        <th>File Upload</th>
                                        <th>Extend Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                </tbody>
                                </table>
                            </div>
                                <!-- Action Buttons -->
                                <div class="row mt-4" >
                                    <div class="d-flex justify-content-end gap-3">
                                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ route('pass_extend') }}'">Cancel</button>
                                        <button type="submit" class="btn btn-primary" onclick="submit_update(event)">Update</button>
                                    </div>
                            </div>
                           
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script>

$(document).ready(function() {
    // Your code here
    loadTableData();
   

});



function get_amount() {

        // Get selected values from the dropdowns
        var accessZone = document.getElementById('access_zone').value;
        // alert(accessZone);
        var accessArea = document.getElementById('access_area').value;
        // alert(accessArea);
        var typeOfPass = document.getElementById('extend_pass_type').value;
        // alert(typeOfPass);
        var passType = document.getElementById('passType').value;
        // alert(passType);
        var persontype = document.getElementById('persontype').value;
        // alert(persontype);
        var vectype = document.getElementById('vectype').value;
        // alert(vectype);
        var visitorType = document.getElementById('visitorType').value;
        // alert(visitorType);

        // Error message span
        var errorSpan = document.getElementById('errorMessage');

        if (passType === 'person') {
            if (accessZone === '67330c704bf0611470') {
// alert('hi');
                if (typeOfPass === 'day') {
                    $.ajax({
                        url: '{{ route("fetch.rate") }}',
                        type: 'GET',
                        data: {
                            access_zone: accessZone,
                            access_area: accessArea,
                            type_of_pass: typeOfPass,
                            passType: passType,
                            persontype: persontype,
                            vectype: vectype,
                            visitorType: visitorType,
                        },
                        success: function (response) {
                           console.log(response.rate);
                            // $('#amount').val(response.rate); // Populate the amount input field
                            $('#extend_gross_amount,#amount_day').val(response.rate);

                            calculateamount(); // Optionally call a function to calculate the amount further
                        },
                        error: function () {
                            showError("Error fetching rate.");
                        },
                    });
                } else {
                    showError("Invalid type of pass for access zone.");
                }
            } 
        } else {
            // alert('helo');
            // AJAX request for non-person pass types
            $.ajax({
                url: '{{ route("fetch.rate") }}',
                type: 'GET',
                data: {
                    access_zone: accessZone,
                    access_area: accessArea,
                    type_of_pass: typeOfPass,
                    passType: passType,
                    persontype: persontype,
                    vectype: vectype,
                    visitorType: visitorType,
                },
                success: function (response) {
                    $('#extend_gross_amount,#amount_day').val(response.rate);

                    // Populate the amount input field
                    calculateamount(); // Optionally call a function to calculate the amount further
                },
                error: function () {
                    showError("Error fetching rate.");
                },
            });
        }

        // Helper function to display error messages
        function showError(message) {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error', // You can change this to 'info', 'warning', etc., based on your needs
                timer: 1000, // Time in milliseconds (10 seconds)
                showConfirmButton: true,
            });
        }
    }

    function periodAmt() {
        const rate = document.getElementById('amount_day').value;
        // alert(rate);
        var no_of_days = document.getElementById('extend_period').value;
        if (no_of_days == 15) {
            $('#extend_gross_amount').val(''); // Populate the extend_gross_amount input field
            $('#extend_gross_amount').val('100'); // Populate the extend_gross_amount input field

            // alert('15 days');
        } else if (no_of_days == 30) {
            $('#extend_gross_amount').val(''); // Populate the extend_gross_amount input field
            $('#extend_gross_amount').val('200');
            // alert('30 days')
        } else {
            $('#extend_gross_amount').val(''); // Populate the extend_gross_amount input field
            $('#extend_gross_amount').val(rate);
        }

    }

    // ne change
 function calculateamount() {
        // Get input values and parse them as numbers
        const amount = parseFloat(document.getElementById('extend_gross_amount').value) || 0;
        const no_of_days = parseFloat(document.getElementById('extend_period').value) || 0;
        const passType = document.getElementById('passType').value;
        const type_of_pass = document.getElementById('extend_pass_type').value;

        //    const  netamount= amount*no_of_days;
        //    const netamounttotal= netamount*

        // Validate inputs
        if (amount <= 0 || no_of_days <= 0) {
            console.error("Invalid input for amount or number of days.");
            return;
        }

        // GST rate and other constants
        const gstRate = 18; // Total GST percentage (9% CGST + 9% SGST)

        // Helper function to calculate GST, CGST, SGST, and gross amount
        const calculateTax = (taxableAmount) => {
            const gstAmount = (taxableAmount * gstRate) / 100;
            const cgstAmount = gstAmount / 2;
            const sgstAmount = gstAmount / 2;
            const grossAmount = taxableAmount + gstAmount;
            const netAmount = taxableAmount; // Net Amount is the base taxable amount (before tax)

            // const netAmount = amount * no_of_days;
            // alert(netAmount);


            return { gstAmount, cgstAmount, sgstAmount, grossAmount ,netAmount};
        };

        let taxableAmount;

        // Logic for different scenarios
        if (passType === 'person' && type_of_pass === 'day') {
            if (no_of_days === 15 || no_of_days === 30) {
                taxableAmount = amount;
            } else {
                taxableAmount = amount * no_of_days;
            }
        } else {
            taxableAmount = amount * no_of_days;
        }

        // Calculate the tax details
        const { gstAmount, cgstAmount, sgstAmount, grossAmount ,netAmount} = calculateTax(taxableAmount);

        // Update the HTML elements
        document.getElementById('sgstLabel').textContent = `₹ ${sgstAmount.toFixed(2)}`;
        document.getElementById('extend_sgst').value = sgstAmount.toFixed(2);

        document.getElementById('cgstLabel').textContent = `₹ ${cgstAmount.toFixed(2)}`;
        document.getElementById('extend_cgst').value = cgstAmount.toFixed(2);

        // document.getElementById('extend_gross_amountlabel').textContent = `₹ ${grossAmount.toFixed(2)}`;
        // document.getElementById('extend_gross_amount').value = grossAmount.toFixed(2);

        document.getElementById('extend_tax_amountlabel').textContent = `₹ ${grossAmount.toFixed(2)}`;
        document.getElementById('extend_tax_amount').value = grossAmount.toFixed(2);


        
        document.getElementById('with_tax_amount_label').textContent = `₹ ${grossAmount.toFixed(2)}`;
        document.getElementById('with_tax_pass_amount').value = grossAmount.toFixed(2);
        
        // document.getElementById('net_amount').value =netAmount.toFixed(2);
        document.getElementById('extend_net_amount').value = netAmount.toFixed(2);
        document.getElementById('extend_net_amountlabel').textContent = `₹ ${netAmount.toFixed(2)}`;
        
    // document.getElementById('net_amountlabel').textContent = `₹ ${netAmount.toFixed(2)}`;

        // Debugging logs
        console.log(`Taxable Amount: ₹${taxableAmount.toFixed(2)}`);
        console.log(`GST(${gstRate}%) Amount: ₹${gstAmount.toFixed(2)}`);
        console.log(`Gross Amount (Inclusive of Tax): ₹${grossAmount.toFixed(2)}`);
        console.log(`Net Amount (Base Amount): ₹${netAmount.toFixed(2)}`);

    }


// Function to load table data
function loadTableData() {
    // event.preventDefault();
    var passId = document.getElementById('pass_id_input').value;
    const rfidInput = $('#rfidInput').val();

    $.ajax({
        type: "GET",
        url: '{{ route("PassSublist1.index") }}',
        data: {
            rfidInput: rfidInput, // Pass the parameter directly
            _token: '{{ csrf_token() }}' // Include CSRF token for security
        },
        success: function (data) {
            // Clear the current table body before appending new rows
            $('#sublist tbody').empty();

            // Populate the table with new data
            let s_no = 1;
            data.forEach(function (item) {
                var newRow = `
                    <tr>
                        <td>${s_no++}</td>
                        <td>${item.extend_pass_type || 'N/A'}</td>
                        <td>${item.extend_period || 'N/A'}</td>
                        <td>${item.extend_to_date || 'N/A'}</td>
                        <td>
                            <a onclick="imageViews('${item.extend_file_upload}')">
                                <img src="{{ asset('public/uploads/dwn.png') }}" width="40px" height="40px">
                            </a>
                        </td>
                        <td>${item.extend_tax_amount || 'N/A'}</td>
                    </tr>
                `;
                $('#sublist tbody').append(newRow); // Append the new row to the table
            });
        },
        error: function (xhr) {
            console.log(xhr.responseText); // Log any errors for debugging
        }
    });
}


    function imageViews(file_name, extend_file_upload) {
    // alert();
    // Split the file names by commas (in case there are multiple)
    var fileNames = file_name.split(',');

    // Base directory wher your files are stored

    var aadhaarDirectory = "/documents/uploads/pass_entry/";
    // var licenceDirectory = "/documents/uploads/person_registration/licence/";

    fileNames.forEach(function(fileName) {
        // Get the file extension
        var cFileName = fileName.split('.');
        var fileExtension = cFileName[cFileName.length - 1].toLowerCase();
        var filePath = `${baseDirectory}${fileName}`; // Construct the full path

        var baseDirectory;

        // if (proof_type == 'Aadhaar') {
        //     baseDirectory = aadhaarDirectory;
        // } else if (proof_type == 'Driving License') {
        //     baseDirectory = licenceDirectory;

        // }
        var filePath = `${aadhaarDirectory}/${fileName}`;
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


function RfidSearch() {
    const rfidInput = $('#rfidInput').val();
    if (rfidInput) {
        $.ajax({
            url: "{{route('getextendPassDetails.index')}}",
            method: "GET",
            data: {
                rfidInput: rfidInput,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#details-tab1').show();

                const pass_main = response.pass_main;

                var person_data = response.data;

                if(person_data){
                pass_main.forEach(item => {
                    if (item.pass_type === "person") {
                        // alert();
                        $('#details-tab1').show();
                        $('#vehicle_details').hide();
                        $('#person_details').show();
                        // console.log(item.firm_address);
                        document.getElementById("person_name").innerText = person_data.full_name;
                        document.getElementById("address").innerText = item.firm_address;
                        document.getElementById("aadhar_no").innerText = person_data.aadhaar_no;
                        document.getElementById("firm_name").innerText = item.company_name;
                        document.getElementById("pass_id").innerText = item.pass_id;
                        document.getElementById("pass_id_input").value = item.pass_id;
                        document.getElementById("from_date").innerText = item.from_date;
                        document.getElementById("old_from_date").value = formatDateForDatetimeLocal(item.from_date);
                        document.getElementById("from_date_display").value = item.from_date;
                        document.getElementById("to_date").innerText = item.to_date;
                        document.getElementById("access_zonelabel").innerText = item.zone_type;
                        document.getElementById("access_arealabel").innerText = item.access_area_name;
                        document.getElementById("period").innerText = item.no_of_days;
                        document.getElementById("amount").innerText = item.amount;

                        document.getElementById("access_area").value = item.access_area;
                        document.getElementById("access_zone").value = item.access_zone;
                        document.getElementById("passType").value = item.pass_type;
                        document.getElementById("persontype").value = item.persontype;
                        document.getElementById("proof_expiry_date_input").value = person_data.expiry_date;

                       
                        
                    } else if (item.pass_type === "vehicle") {
                        $('#vehicle_details').show();
                        $('#person_details').hide();
                        $('#details-tab1').show();
                        // console.log(item.firm_address);
                        document.getElementById("vech_no").innerText = person_data.vech_reg_no;
                        document.getElementById("owner_name").innerText = person_data.owner_name;
                        document.getElementById("address").innerText = item.firm_address;
                        document.getElementById("pass_id").innerText = item.pass_id;
                        document.getElementById("pass_id_input").value = item.pass_id;
                        document.getElementById("from_date").innerText = item.from_date;
                        document.getElementById("expiry_date").innerText = item.to_date;
                        document.getElementById("old_from_date").value = formatDateForDatetimeLocal(item.from_date);
                        document.getElementById("from_date_display").value = item.from_date;
                        document.getElementById("to_date").innerText = item.to_date;

                        document.getElementById("access_zonelabel").innerText = item.zone_type;
                        document.getElementById("access_arealabel").innerText = item.access_area_name;
                        document.getElementById("period").innerText = item.no_of_days;
                        document.getElementById("amount").innerText = item.amount;
                        document.getElementById("access_area").value = item.access_area;
                        document.getElementById("access_zone").value = item.access_zone;
                        document.getElementById("firm_name").innerText = item.company_name;
                        document.getElementById("vectype").value = item.vectype;
                        document.getElementById("visitorType").value = item.student_type;
                        document.getElementById("passType").value = item.pass_type;
                        // document.getElementById("proof_expiry_date_input").value = person_data.expiry_date;


                    // document.getElementById("proof_expiry_date").innerText = licenseData.expiry_date;
                      

                        // 672f3e2e33d1696167
                            // Handle certificate types
                    switch (item.certificate_type) {
                        // console.log(item.certificate_type);
                        // Insurance
                        case '672f3e3d632fa69886':
                            // document.getElementById('ins_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('ins_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        // Fitness
                        case '672f3e2e33d1696167':
                            // document.getElementById('fc_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('fc_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        // PUC
                        case '672f3e67e5df337110':
                            // document.getElementById('puc_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('puc_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        // Light - Insurance
                        case '672f3eb91192d39247':
                            // document.getElementById('ins_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('ins_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        // Light - Fitness
                        case '672f3ea5ba0a951850':
                            // document.getElementById('fc_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('fc_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        // Light - PUC
                        case '672f3ec42b90931412':
                            // document.getElementById('puc_valid_up_to').innerText = item.valid_up_to || 'N/A';
                            document.getElementById('puc_valid_up_to_input').value = item.valid_up_to || '';
                            break;

                        default:
                            console.warn('Unknown certificate type:', item.certificate_type);
                        }
                    }
               
                

                   
                });
            }
            else{
                const regError = document.getElementById('rfidInputerror');
                regError.style.display = "block";
                $('#vehicle_details').hide();
                $('#person_details').hide();
                $('#details-tab1').hide();
            }
            }
        });
    }
}
    function submit_update(event) {
    // Prevent default form submission behavior
    event.preventDefault();
    // Collect input values
    var extend_pass_type = document.getElementById('extend_pass_type').value;
    var extend_period = document.getElementById('extend_period').value;
    var extend_to_date = document.getElementById('date_display').value;
    // alert(extend_to_date);
    var extend_tax_amount = document.getElementById('extend_tax_amount').value;
    var extend_gross_amount = document.getElementById('extend_gross_amount').value;
    var extend_net_amount = document.getElementById('extend_net_amount').value;
    var extend_cgst = document.getElementById('extend_cgst').value;
    var extend_sgst = document.getElementById('extend_sgst').value;
    var passId = document.getElementById('pass_id_input').value;

    // Check if passId is provided
    if (!passId) {
        alert("Please enter a PassID NO.");
        return;
    }
    var extend_file_upload = document.getElementById('extend_file_upload').files[0];
    // Create FormData object and append data
    var formData = new FormData();
    formData.append('extend_pass_type', extend_pass_type);
    formData.append('extend_period', extend_period);
    formData.append('extend_to_date', extend_to_date);
    formData.append('extend_tax_amount', extend_tax_amount);
    formData.append('extend_gross_amount', extend_gross_amount);
    formData.append('extend_net_amount', extend_net_amount);
    formData.append('extend_cgst', extend_cgst);
    formData.append('extend_sgst', extend_sgst);
    formData.append('extend_file_upload', extend_file_upload);
    formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for Laravel

    // Make the AJAX POST request
    $.ajax({
        type: "POST",
        url: "{{ route('pass-update.update', ':id') }}".replace(':id', passId), // Dynamically replace ':id' with passId
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                // sweetalert("update");
                sweetalert("update");
                window.location.href = "{{ route('pass_extend') }}";
                // alert('Pass updated successfully!');
                // loadTableData();
                // Perform additional actions here, like redirecting or updating the UI
            } else {
                alert('Update failed. ' + (response.message || 'Please try again.'));
            }
        },
        error: function(error) {
            console.error('Error:', error);
            alert('An error occurred while updating the pass.');
        }
    });
}


// // new changes
// function sublist_add(event) {
//     // Prevent default form submission behavior
//     event.preventDefault();

//     const insValue = $('#ins_valid_up_to_input').val();
//     const fcValue = $('#fc_valid_up_to_input').val();
//     const pucValue = $('#puc_valid_up_to_input').val();

//         const currentDate = new Date();

//     const insDate = new Date(insValue);
//     const fcDate = new Date(fcValue);
//     const pucDate = new Date(pucValue);
//     const dldate = new Date(proof_expiry_date);
//     // Remove time components for accurate comparison (only date)
//     insDate.setHours(0, 0, 0, 0);
//     fcDate.setHours(0, 0, 0, 0);
//     pucDate.setHours(0, 0, 0, 0);
//     dldate.setHours(0, 0, 0, 0);
//     currentDate.setHours(0, 0, 0, 0);


//     // Collect input values
//     var extend_pass_type = document.getElementById('extend_pass_type').value;
//     var extend_period = document.getElementById('extend_period').value;
//     var extend_to_date = document.getElementById('date_display').value;
//     var extend_tax_amount = document.getElementById('extend_tax_amount').value;
//     var extend_gross_amount = document.getElementById('extend_gross_amount').value;
//     var extend_net_amount = document.getElementById('extend_net_amount').value;
//     var extend_cgst = document.getElementById('extend_cgst').value;
//     var extend_sgst = document.getElementById('extend_sgst').value;
//     var passId = document.getElementById('pass_id_input').value;
// // alert(passId);
//     // Check if passId is provided
//     if (!passId) {
//         alert("Please enter a PassID NO.");
//         return;
//     }
//     var extend_file_upload = document.getElementById('extend_file_upload').files[0];


//     if (passType == 'vehicle') {
//         // Check if the expiry date is before the current date
//         if (insDate != '' && fcDate != '' && pucDate != '') {
//             // alert('hi vehicle document');
//             if (insDate < currentDate || fcDate < currentDate || pucDate < currentDate) {
//                 // if (insDate >=currentDate && fcDate >= currentDate && $pucDate >= $currentDate) {
//                 // alert("The date has expired.");
//                 Swal.fire({
//                     title: 'Expired',
//                     text: 'The Vehicle Document Date was Expired!..',
//                     icon: 'info',
//                     timer: 1500, // Time in milliseconds (2 seconds)
//                     showConfirmButton: true
//                 });
//             } else {

//         // Create FormData object and append data
//         var formData = new FormData();
//         formData.append('extend_pass_type', extend_pass_type);
//         formData.append('extend_period', extend_period);
//         formData.append('extend_to_date', extend_to_date);
//         formData.append('extend_tax_amount', extend_tax_amount);
//         formData.append('extend_gross_amount', extend_gross_amount);
//         formData.append('extend_net_amount', extend_net_amount);
//         formData.append('extend_cgst', extend_cgst);
//         formData.append('extend_sgst', extend_sgst);
//         formData.append('extend_file_upload', extend_file_upload);
//         formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for Laravel

//     // Make the AJAX POST request
//     $.ajax({
//         type: "POST",
//         url: "{{ route('pass-update.update', ':id') }}".replace(':id', passId), // Dynamically replace ':id' with passId
//         data: formData,
//         contentType: false,
//         processData: false,
//         success: function(response) {
//             if (response.success) {
//                 // sweetalert("update");
//                 // sweetalert("update");
//                 sweetalert("add")
//                 // window.location.href = "{{ route('pass_extend') }}";
//                 // alert('Pass updated successfully!');
//                 loadTableData();
//                 // Perform additional actions here, like redirecting or updating the UI
//             } else {
//                 alert('Update failed. ' + (response.message || 'Please try again.'));
//             }
//         },
//         error: function(error) {
//             console.error('Error:', error);
//             // alert('An error occurred while updating the pass.');
//         }
//     });


// }
//         } else {
//             Swal.fire({
//                 title: 'Error',
//                 text: 'Missing Vehicle Document Details',
//                 icon: 'info',
//                 timer: 1500, // Time in milliseconds (2 seconds)
//                 showConfirmButton: true
//             });
//             // alert("date is valid");
//         }
//     }else{
//         if(persontype == '66fe76256d3a792499'){
//             if(dldate != '' && dldate < currentDate){
//                 Swal.fire({
//                     title: 'Expired',
//                     text: 'The Vehicle Document Date was Expired!..',
//                     icon: 'info',
//                     timer: 1500, // Time in milliseconds (2 seconds)
//                     showConfirmButton: true
//                 });
//             }else{

//                   // Create FormData object and append data
//         var formData = new FormData();
//         formData.append('extend_pass_type', extend_pass_type);
//         formData.append('extend_period', extend_period);
//         formData.append('extend_to_date', extend_to_date);
//         formData.append('extend_tax_amount', extend_tax_amount);
//         formData.append('extend_gross_amount', extend_gross_amount);
//         formData.append('extend_net_amount', extend_net_amount);
//         formData.append('extend_cgst', extend_cgst);
//         formData.append('extend_sgst', extend_sgst);
//         formData.append('extend_file_upload', extend_file_upload);
//         formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for Laravel

//     // Make the AJAX POST request
//     $.ajax({
//         type: "POST",
//         url: "{{ route('pass-update.update', ':id') }}".replace(':id', passId), // Dynamically replace ':id' with passId
//         data: formData,
//         contentType: false,
//         processData: false,
//         success: function(response) {
//             if (response.success) {
//                 // sweetalert("update");
//                 // sweetalert("update");
//                 sweetalert("add")
//                 // window.location.href = "{{ route('pass_extend') }}";
//                 // alert('Pass updated successfully!');
//                 loadTableData();
//                 // Perform additional actions here, like redirecting or updating the UI
//             } else {
//                 alert('Update failed. ' + (response.message || 'Please try again.'));
//             }
//         },
//         error: function(error) {
//             console.error('Error:', error);
//             // alert('An error occurred while updating the pass.');
//         }
//     });
//  }
// }
// }else{
//                // Create FormData object and append data
//                var formData = new FormData();
//         formData.append('extend_pass_type', extend_pass_type);
//         formData.append('extend_period', extend_period);
//         formData.append('extend_to_date', extend_to_date);
//         formData.append('extend_tax_amount', extend_tax_amount);
//         formData.append('extend_gross_amount', extend_gross_amount);
//         formData.append('extend_net_amount', extend_net_amount);
//         formData.append('extend_cgst', extend_cgst);
//         formData.append('extend_sgst', extend_sgst);
//         formData.append('extend_file_upload', extend_file_upload);
//         formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for Laravel

//     // Make the AJAX POST request
//     $.ajax({
//         type: "POST",
//         url: "{{ route('pass-update.update', ':id') }}".replace(':id', passId), // Dynamically replace ':id' with passId
//         data: formData,
//         contentType: false,
//         processData: false,
//         success: function(response) {
//             if (response.success) {
//                 // sweetalert("update");
//                 // sweetalert("update");
//                 sweetalert("add")
//                 // window.location.href = "{{ route('pass_extend') }}";
//                 // alert('Pass updated successfully!');
//                 loadTableData();
//                 // Perform additional actions here, like redirecting or updating the UI
//             } else {
//                 alert('Update failed. ' + (response.message || 'Please try again.'));
//             }
//         },
//         error: function(error) {
//             console.error('Error:', error);
//             // alert('An error occurred while updating the pass.');
//         }
//     });

// }

function sublist_add(event) {
    // Prevent default form submission behavior
    event.preventDefault();


// Collect input values
    var extend_pass_type = document.getElementById('extend_pass_type').value;
    var extend_period = document.getElementById('extend_period').value;
    var extend_to_date = document.getElementById('date_display').value;
    var extend_tax_amount = document.getElementById('extend_tax_amount').value;
    var extend_gross_amount = document.getElementById('extend_gross_amount').value;
    var extend_net_amount = document.getElementById('extend_net_amount').value;
    var extend_cgst = document.getElementById('extend_cgst').value;
    var extend_sgst = document.getElementById('extend_sgst').value;
    var passId = document.getElementById('pass_id_input').value;
    var passType = document.getElementById('passType').value;
    var proof_expiry_date = $('#proof_expiry_date_input').val();
    var persontype = $('#persontype').val();
    // alert(persontype);
    
    // alert(proof_expiry_date);

    const insValue = $('#ins_valid_up_to_input').val();
    // alert(insValue);
    const fcValue = $('#fc_valid_up_to_input').val();
    // alert(fcValue);
    const pucValue = $('#puc_valid_up_to_input').val();
    // alert(pucValue);
    
    const currentDate = new Date();
    const insDate = new Date(insValue);
    const fcDate = new Date(fcValue);
    const pucDate = new Date(pucValue);
    const dldate = new Date(proof_expiry_date);

    // Remove time components for accurate comparison (only date)
    insDate.setHours(0, 0, 0, 0);
    fcDate.setHours(0, 0, 0, 0);
    pucDate.setHours(0, 0, 0, 0);
    dldate.setHours(0, 0, 0, 0);
    currentDate.setHours(0, 0, 0, 0);
    // alert(passType);

    if (!passId) {
        alert("Please enter a PassID NO.");
        return;
    }

    var extend_file_upload = document.getElementById('extend_file_upload').files[0];

    if(passType === 'vehicle') {

        if (insValue && fcValue && pucValue) {
            if (insDate < currentDate || fcDate < currentDate || pucDate < currentDate) {
                // alert('hi');
                Swal.fire({
                    title: 'Expired',
                    text: 'The Vehicle Document Date was Expired!..',
                    icon: 'info',
                    timer: 1500,
                    showConfirmButton: true
                });
            } else {
                // alert('hello');
                updatePass();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Missing Vehicle Document Details',
                icon: 'info',
                timer: 1500,
                showConfirmButton: true
            });
        }
    } else if (persontype === '66fe76256d3a792499') {
        // 66fe76af145a071845
        // alert('data');
        if (dldate && dldate < currentDate) {
            Swal.fire({
                title: 'Expired',
                text: 'The Vehicle Document Date was Expired!..',
                icon: 'info',
                timer: 1500,
                showConfirmButton: true
            });
        } else {
            // alert('hi');
            updatePass();
        }
    } else {
        updatePass();
    }

    function updatePass() {
        var formData = new FormData();
        formData.append('extend_pass_type', extend_pass_type);
        formData.append('extend_period', extend_period);
        formData.append('extend_to_date', extend_to_date);
        formData.append('extend_tax_amount', extend_tax_amount);
        formData.append('extend_gross_amount', extend_gross_amount);
        formData.append('extend_net_amount', extend_net_amount);
        formData.append('extend_cgst', extend_cgst);
        formData.append('extend_sgst', extend_sgst);
        formData.append('extend_file_upload', extend_file_upload);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            type: "POST",
            url: "{{ route('pass-update.update', ':id') }}".replace(':id', passId),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    sweetalert("add");
                    loadTableData();
                } else {
                    alert('Update failed. ' + (response.message || 'Please try again.'));
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
}


function get_vec_pass() {
    const noOfDays = parseInt(document.getElementById('extend_period').value);
    // alert(noOfDays);
    const type_of_pass = document.getElementById('extend_pass_type').value;
    const amount = document.getElementById('extend_gross_amount').value;
    if (type_of_pass != ""){
    document.getElementById('extend_period').value = "1";
    document.getElementById('extend_gross_amount').value = "";
    document.getElementById('extend_to_date').value = "";

}
}
// Event listener to call calculateToDate when the number of days changes
document.getElementById('extend_period').addEventListener('input', calculateToDate);
document.getElementById('old_from_date').addEventListener('input', calculateToDate);



function calculateToDate() {
    const fromDateInput = document.getElementById('old_from_date');
    // alert(fromDateInput);
    const noOfDaysInput = document.getElementById('extend_period');
    // alert(noOfDaysInput);
    const typeOfPassInput = document.getElementById('extend_pass_type');
    // alert(typeOfPassInput);

    const noOfDays = parseInt(noOfDaysInput.value, 10) || 1; // Default to 1 if empty
    const type_of_pass = typeOfPassInput.value;

    if (type_of_pass === "day") {
        const fromDateValue = fromDateInput.value;

        // Check if the from date is valid
        if (!fromDateValue) {
            alert("Please select a valid from date.");
            return;
        }

        // Create a new Date object from the from date input
        const fromDate = new Date(fromDateValue);

        // Check if the date is valid
        if (isNaN(fromDate.getTime())) {
            alert("Invalid date. Please check the input.");
            return;
        }

        // Update the display for the from date in the desired format
        document.getElementById('from_date_display').value = formatDateForDisplayWithTime(fromDate);

        // Add the number of days
        fromDate.setDate(fromDate.getDate() + noOfDays);

        // Subtract 1 minute
        fromDate.setMinutes(fromDate.getMinutes() - 1);

        // Format the to date for storage (datetime-local)
        const year = fromDate.getFullYear();
        const month = String(fromDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(fromDate.getDate()).padStart(2, '0');
        const hours = String(fromDate.getHours()).padStart(2, '0');
        const minutes = String(fromDate.getMinutes()).padStart(2, '0');

        // Combine for datetime-local storage
        const toDateStorage = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the to_date input for storage
        document.getElementById('extend_to_date').value = toDateStorage;

        // Format for display (AM/PM format)
        const displayHours = fromDate.getHours();
        const ampm = displayHours >= 12 ? 'PM' : 'AM';
        const displayHour12 = (displayHours % 12) || 12; // Convert to 12-hour format
        const formattedDisplayDate =
            `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;

        // Display the formatted date
        const displayElement = document.getElementById('date_display');
        displayElement.value = formattedDisplayDate;

    }
    }


function formatDateForDatetimeLocal(inputDate) {
    // Parse the input string (assuming format is "YYYY-MM-DD HH:MM AM/PM")
    const [date, time, period] = inputDate.split(' ');
    const [hours, minutes] = time.split(':');
    let formattedHours = parseInt(hours);

    // Convert to 24-hour format if needed
    if (period === 'PM' && formattedHours < 12) {
        formattedHours += 12;
    } else if (period === 'AM' && formattedHours === 12) {
        formattedHours = 0;
    }

    // Return in "YYYY-MM-DDTHH:MM" format
    return `${date}T${String(formattedHours).padStart(2, '0')}:${minutes}`;
}


function formatDateForDisplayWithTime(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHour12 = (hours % 12) || 12; // Convert to 12-hour format

    return `${year}-${month}-${day} ${String(displayHour12).padStart(2, '0')}:${minutes} ${ampm}`;
}



</script>

@endsection