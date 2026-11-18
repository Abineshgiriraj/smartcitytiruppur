@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row   page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Pass/</span>Pass Request</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}

.password-container {
    position: relative;
}

.input-wrapper {
    position: relative;
}

.input-wrapper .form-control {
    padding-right: 30px;
}

.input-wrapper .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
}

.font-weight-bold {
    font-weight: bold;
}
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div id="basicwizard">
                    <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                        <li class="nav-item">
                            <a href="#basictab1" data-bs-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                <span class="d-sm-inline">Agents Details</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a id="tab2-link" href="#basictab2" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2">
                                <span class="d-sm-inline">Pass Entry </span>
                            </a>
                        </li>

                    </ul>


                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active" id="basictab1">
                            <form id="academicYearForm">
                                @csrf
                                @if(isset($acc_year))
                                @method('PUT')
                                @endif


                                <div class="row mb-3 mt-2">
                                    <div class="col-sm-2  ">
                                        <label for="nationalitySelect" class="col-form-label">Type of Firms /
                                            Agent</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>Stevedore</b></label>
                                    </div>



                                    <div class="col-sm-2  ">
                                        <label class="col-form-label" for="basic-default-fullname">Firm ID:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>20240900001</b></label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Company Reg No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>AA3225fr6586</b></label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>Tuticorin Lorry Owners
                                                Association</b></label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Mobile no</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>9090909018</b></label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>tutlorryass@gmail.com</b></label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Contact Person</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>Deepak</b></label>

                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">FAX No</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>-</b></label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">GSTIN</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>-</b></label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Address 1</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="colFormLabelSm" class="col-form-label">Harbour Main Road</label>
                                    </div>

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Address 2</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="colFormLabelSm" class="col-form-label">Tuticorin</label>

                                    </div>

                                </div>
                                <div class="btn-float">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.querySelector('#tab2-link').click();">Next</button>
                                </div>

                        </div>

                        <div class="tab-pane" id="basictab2">

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Pass Type</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-select" id="passType" name="passType" onchange="pass_type()">
                                        <option value="">Select PassType</option>
                                        <option value="vehicle">Vehicle Pass</option>
                                        <option value="person">Person Pass</option>
                                    </select>
                                </div>

                                <div class="col-sm-2" id="vehicle_div" style="display:none;">
                                    <label for="colFormLabelSm" class="col-form-label">Reg. Vehicle Number</label>
                                </div>
                                <div class="col-sm-4" id="vehicle_div1" style="display:none;">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                                        oninput="vehicle_data()" />
                                </div>

                                <div id="person_div" class="col-sm-2" style="display:none;">
                                    <label for="colFormLabelSm" class="col-form-label">Adhaar No.</label>
                                </div>
                                <div id="person_div1" class="col-sm-4" style="display:none;">
                                    <input type="text" class="form-control" id="adhaar" name="adhaar"
                                        oninput="person_data()" />
                                </div>

                            </div>
                            <div id="vehicle_details" style="display:none;">
                                <div class="row">

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Purpose of
                                            Cargo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Cargo 1</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Card Number</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">2422fe1111</label>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Type of Vehicle</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Truck</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">EIR Number</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">EIR0001</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Insurance Valid
                                            UPTO</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label ">02/08/2025</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">FC Valid UPTO</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">15/02/2025</label>
                                    </div>
                                </div>
                                <div class=" row ">

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">PUC Valid UPTO</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">12/12/2024</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">RC Attach</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <img src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                            style="width: 24px; height: 24px;">
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Passing Weight</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                    </div>

                                </div>
                            </div>
                            <div id="person_details" style="display:none;">
                                <div class="row ">


                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Dinesh</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Blood Group</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">A+</label>

                                    </div>

                                </div>
                                <div class="row ">


                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Designation</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Person</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Card Number</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">2422fe1111</label>
                                    </div>
                                </div>
                                <div class="row ">

                                    <div class=" col-sm-2 ">
                                        <label for=" colFormLabelSm" class="col-form-label">Nationality</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Indian</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Country</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">India</label>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">VISA No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">12345678764325</label>

                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Mobile</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">9898989098</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">E-Mail ID</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">dinesh@gmail.com</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Type of ID Proof</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">Adhaar Card</label>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">ID Proof No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  ">3446567511</label>
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">ID Proof Attach</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <img onclick="pdf()" target="_blank"
                                            src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                            style="width: 24px; height: 24px;">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Photo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <img onclick="pdf()" target="_blank"
                                            src="resources/views/content/person_registration/PDF.png" alt="PDF"
                                            style="width: 24px; height: 24px;">
                                    </div>
                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Vehicle No.</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="adhaar" name="adhaar" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Access Gate</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-select">
                                        <option value="">Select Access Gate</option>
                                        <option value="1">Inside/Outside</option>
                                        <option value="2">Custom Bound Area</option>
                                        <option value="3">Zone A & B</option>
                                    </select>
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Access Area</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-select me-2" name="cargo" id="cargo">
                                        <option value="SBW">SBW</option>
                                        <option value="NBW">NBW</option>
                                        <option value="NCB">NCB</option>
                                        <option value="zone_b">Zone B</option>
                                    </select>
                                </div>
                            </div>
                            <hr>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Type of Pass</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-select me-2" name="cargo" id="cargo">
                                        <option value="day">Day</option>
                                    </select>
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Access Area</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                                        value="1" />
                                </div>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">From Date</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">To Date</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Amount</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">SGST</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                            </div>
                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">CGST</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">C Cost</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                            </div>
                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">C SGST</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">C CGST</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                            </div>
                            <div class="row mb-3 mt-2">
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">C Net</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                                <div class="col-sm-2  ">
                                    <label for="colFormLabelSm" class="col-form-label">Gross</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" />
                                </div>
                            </div>

                            <div class="justify-content-center" style="float: right;">
                                <button id="openTableButton" class="btn btn-primary">Add</button>
                            </div>

                            <br><br>
                            <div id="dataTableContainer" class="table-responsive ">
                                <table id="example" class="table table-striped table-bordered" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>RFID Card No.</th>
                                            <th>Registration No.</th>
                                            <th>Pass Type</th>
                                            <th>No of Days</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>9867585</td>
                                            <td>RFD1213131</td>
                                            <td>Day</td>
                                            <td>1</td>
                                            <td>02.09.2024</td>
                                            <td>02.09.2024</td>
                                            <td>120</td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="text-primary mr-2">
                                                        <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                                    </a>
                                                    <span class="mx-1"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>9067235</td>
                                            <td>RFD1210101</td>
                                            <td>Day</td>
                                            <td>2</td>
                                            <td>05.09.2024</td>
                                            <td>06.09.2024</td>
                                            <td>240</td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="text-primary mr-2">
                                                        <i class="fas fa-edit"></i> <!-- Edit Icon -->
                                                    </a> <span class="mx-1"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-3">
                                    <label for=" colFormLabelSm" class="col-form-label">Total Pass Amount</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="pass_amount" name="pass_amount"
                                        value="360" />
                                </div>

                                <div class="col-sm-4">
                                    <button type="button" id="openTableButton" class="btn btn-primary"
                                        onclick="window.location.href='{{ url('/payment_option')}}'">Payment</button>
                                </div>

                            </div>

                            <div class="btn-float">
                                <button type="button" class="btn btn-danger"
                                    onclick="window.location.href='{{ url('/pass_request_from_agent')}}'">Cancel</button>
                                <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button>
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->


    <script>
    function pass_type() {
        const passType = document.getElementById('passType').value;

        // Hide both vehicle and person divs initially
        document.getElementById("vehicle_div").style.display = "none";
        document.getElementById("vehicle_div1").style.display = "none";
        document.getElementById("person_div").style.display = "none";
        document.getElementById("person_div1").style.display = "none";

        // Show the appropriate div based on selected option
        if (passType === 'vehicle') {
            document.getElementById("vehicle_div").style.display = "block";
            document.getElementById("vehicle_div1").style.display = "block";
        } else if (passType === 'person') {
            document.getElementById("person_div").style.display = "block";
            document.getElementById("person_div1").style.display = "block";
        }
    }


    function vehicle_data() {
        // vehicle_details
        const vehicle_validate = document.getElementById('vehicle_no').value;

        document.getElementById("vehicle_details").style.display = "none";
        if (vehicle_validate !== '') {
            document.getElementById('adhaar').value = '';
            document.getElementById("person_details").style.display = "none";
            document.getElementById("vehicle_details").style.display = "block";
        }
    }

    function person_data() {
        const person_validate = document.getElementById('adhaar').value;

        document.getElementById("person_details").style.display = "none";
        if (person_validate !== '') {
            document.getElementById('vehicle_no').value = '';
            document.getElementById("vehicle_details").style.display = "none";
            document.getElementById("person_details").style.display = "block";
        }

    }
    </script>


    @endsection