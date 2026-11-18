@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Inward Module/</span>Invoice</h4>
        </div>
    </div>

    <style>
        /* btn style */
        .btn-right {
            float: right;
        }

        .form-select1 {
            width: 10%;
        }

        tr {
            background-color: white !important;
        }



        hr {
            border-top: 2px solid red;
        }

        .select2-container {
            width: 100% !important;
        }

        .tab-details {
            padding: 40px 30px;
            border: 1px solid #cce1b3;
            border-radius: 10px;
            background-color: #fdfffa;
        }
        .height_style{font-size: .95rem !important;  line-height: 1 !important;}
    </style>
    <!-- Add Bootstrap CSS (include in your <head> if not already included) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />


    @php
        $date = date('Y-m-d');
        $supplier = supplier_name();
        $branch = branch_name();
        $location_select = location_name();
    @endphp




    <div class="container mt-4">
        <!-- Nav tabs -->
        <!-- <ul class="nav nav-tabs pb-3" id="purchaseTabs" role="tablist">
                                                                                                                                                                                                                                    <li class="nav-item" role="presentation">
                                                                                                                                                                                                                                        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button"
                                                                                                                                                                                                                                            role="tab">Supplier & Invoice Details</button>
                                                                                                                                                                                                                                    </li>

                                                                                                                                                                                                                                    <li class="nav-item" role="presentation">
                                                                                                                                                                                                                                        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button"
                                                                                                                                                                                                                                            role="tab">Product Details</button>
                                                                                                                                                                                                                                    </li>


                                                                                                                                                                                                                                </ul> -->
        <div class="card">
            <form id="invoice_form">
                <!-- Tab content -->
                <div class="tab-content border border-top-0 p-4" id="purchaseTabsContent">
                    <!-- Tab 1: Supplier & Invoice Details -->
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel">


                        <div class="row justify-content-center">
                            <div class="col-4  text-center;">
                                <label class="form-label">Quotation No</label>
                                <select class="form-control select2" id="quotation_id" name="quotation_id"
                                    onchange="showSupplierDetails()">
                                    <option value="">Select Quotation No</option>
                                    <option value="ASC25071212">AURQTN-250725-0001</option>
                                    <option value="ASC25074334">AURQTN-250725-0002</option>
                                </select>
                            </div>
                        </div>

                        <div id="details" style="display: none;">
                            <div class="row mt-4">

                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="tab-details">
                                        <div id="supplier_details" style="display:block" class="">
                                            <h6 class="text-uppercase text-body-primary"
                                                style="font-size: large; color: #878a99;">
                                                Supplier Details</h6>
                                            <h6 class="mb-2" style="font-size: large; color: #506018;"><strong>Ss Gobi
                                                    Enterprises</strong></h6>
                                            <p class="mb-1 text-color" style="font-size: 14px;"><i data-feather="map-pin"
                                                    style="width:15px; height:15px; color:#506018"></i> 34M Murugar Kovil St
                                                Namakkal Tamil Nadu</p>
                                            <p class="mb-1 text-color" style="font-size: 14px;"><i data-feather="phone"
                                                    style="width:15px; height:15px; color:#506018"></i> 999999999
                                            </p>
                                            <div class="pb-3">
                                                <p class="mb-1 text-color"><span style="padding-right:50px;">GST No
                                                    </span><span id="gst_no"><b> : 22AAAAA0000A1Z5 </b></span></p>
                                                <p class="mb-1"><span style="padding-right:58px">Name </span><span
                                                        id="gst_no"><b> : Ram</b></span></p>
                                                <p class="mb-1"><span style="padding-right:15px">Contact No </span><span
                                                        id="gst_no"><b> : 8931281934</b></span></p>
                                                <p class="mb-1"><span style="padding-right:54px">Mail Id </span><span
                                                        id="gst_no"><b> : ramsample@gmail.com</b></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="tab-details">
                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="purchas_no">Purchase No <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <h5><b>PO-250729-0001</b></h5>
                                                <!-- <input type="text" class="form-control" id="purchas_no" name="purchas_no"
                                                                                                                                                                                                                                                                                                    oninput="validateInputName(this)"> -->
                                            </div>
                                        </div>

                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="purchas_date">Purchase Date <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" id="purchas_date"
                                                    name="purchas_date" value="<?php echo $date;?>">
                                            </div>
                                        </div>

                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="inv_no">Invoice No. <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inv_no" name="inv_no">
                                            </div>
                                        </div>

                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="inv_date">Invoice Date <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" id="inv_date" name="inv_date"
                                                    value="<?php echo $date;?>">
                                            </div>
                                        </div>

                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="">Invoice Upload <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="file" class="form-control" id="inv_file" name="inv_file">
                                            </div>
                                        </div>

                                        <div class="row mb-1 align-items-center">
                                            <label class="col-sm-4 form-label" for="">Quotation Value<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="col-sm-8">
                                                <h5>₹ 20,000.00</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Next Button -->
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-danger me-1"
                                    onclick="window.location.href='{{ route('invoice') }}'">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="nextTab(1)">Next</button>
                            </div>

                        </div>




                        <hr>

                    </div>



                    <div class="tab-pane fade" id="tab2" role="tabpanel">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="item_name" class="form-label">Product Description</label>
                                <textarea class="form-control" name="prd_desc" id="prd_desc" rows="2"></textarea>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="quantity_nos" class="form-label text-muted small">HSN/SAC</label>
                                <input type="text" class="form-control mt-1" id="quantity_nos" name="quantity_nos"
                                    onkeypress="number_only(event)" onkeyup="get_purchase_order_item_amount_calculation()">
                            </div>

                            <div class="form-group col-md-1">
                                <label for="quantity_nos" class="form-label">Qty</label>
                                <input type="text" class="form-control mt-1" id="quantity_nos" name="quantity_nos"
                                    onkeypress="number_only(event)" onkeyup="get_purchase_order_item_amount_calculation()">
                            </div>


                            <div class="form-group col-md-1">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="text" class="form-control mt-1" id="rate" name="rate"
                                    onkeypress="number_only(event)" onkeyup="get_purchase_order_item_amount_calculation()">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" class="form-control mt-1" id="amount" name="amount" readonly>
                            </div>

                            <div class="form-group col-md-1 mt-4 text-end">
                                <label>&nbsp;</label>
                                <button type="button" id="btn_add"
                                    class="btn btn-primary purchase_entry_item_add_update_btn"
                                    onclick="purchase_entry_item_add_update()">Add</button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <!-- Table Begiins -->
                                    <table id="purchase_entry_sub_datatable" class="table table-bordered table-md"
                                        style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Particulars</th>
                                                <th>HSN/SAC</th>
                                                <th>Qty</th>
                                                <th>Rate(Per Item)</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1.</td>
                                                <td>Non working IT Device Assets as per the Annexure</td>
                                                <td>85499900</td>
                                                <td>1,653</td>
                                                <td>602.69</td>
                                                <td>9,96,248</td>
                                                <td><i class="fas fa-edit text-primary"></i><i
                                                        class="fas fa-trash-alt text-danger"></i></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="text-align: right;">Total</td>
                                                <td colspan="2"><b>9,96,248<b></td>
                                            </tr>
                                        </tbody>

                                    </table>

                                    <!-- Table Ends -->
                                </div>
                                <!-- <div style="margin-left: 50.5%; margin-top: 15px;"> 

                                                                                                                                                                                                                                                                                        <div class="d-flex">
                                                                                                                                                                                                                                                                                            <label class="me-2 mb-2" style="font-size: 1.0rem;">Gross Amount:</label>
                                                                                                                                                                                                                                                                                            <span class="fw-bold ms-3" style="font-size: 1.5rem;">1,18,000</span>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div> -->

                                <!-- Replace the div for Gross Amount with this -->
                                <div class="row mb-3 mt-2 align-items-center" style="">
                                    <div class=" col-md-8"></div>
                                    <div class=" col-md-2 " style="text-align: right;">

                                        <label class="form-label" >Gross Amount</label>
                                    </div>
                                    <div class="col-md-2 text-end" style="">

                                        <span id="grossAmount" class="fw-bold"
                                            style="width: 200px; text-align: right;font-size: 19px; font-weight:bold">9,96,248</span>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <br>
                        <div class="form-group row ">
                            <div class="col-md-5">
                                <div class="row mb-3">
                                    <div class="col-md-9">
                                        <label class="form-label" for="branch_name"> Payment Type <span
                                                class="text-danger fw-bold">*</span>
                                        </label>
                                        <select class="form-control" id="pay_type" name="pay_type"
                                            onchange="get_bankdetails()">
                                            <option value="">Select</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Credit">Credit</option>
                                            <option value="NEFT">NEFT</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="bankdetails" style="display: none;">
                                            <div class="row mt-2">
                                                <div class="col-md-12 mb-3">
                                                    <div class="mb-2">
                                                        <h6 class="text-uppercase text-body-primary mb-0"
                                                            style="font-size: large; color: #878a99;">
                                                            BANK DETAILS
                                                        </h6><br>

                                                        <table class="table table-borderless mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <td
                                                                        style="color: #506018; font-weight: 600; width: 160px;">
                                                                        Bank Name</td>
                                                                    <td>: State Bank Of India</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color: #506018; font-weight: 600;">Branch
                                                                    </td>
                                                                    <td>: Thindal</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color: #506018; font-weight: 600;">Account
                                                                        Holder</td>
                                                                    <td>: Selvanayagan</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color: #506018; font-weight: 600;">Account
                                                                        Number</td>
                                                                    <td>: 4838290101001</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color: #506018; font-weight: 600;">IFSC Code
                                                                    </td>
                                                                    <td>: SBI99999</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-1" style="border-left:1px solid #ddd;"></div>
                            <div class="col-md-6">


                                <table width="100%"
                                    style="background-color: white; border-spacing: 0; border-collapse: collapse;">
                                    <tr>
                                        <td height="20%" align="left" style="padding: 1px 10px;">
                                            <input type="checkbox" id="customs_duty_checkbox" name="customs_duty_checkbox"
                                                style="margin-right: 5px;">
                                            <label class="form-label" for="customs_duty_checkbox">Customs
                                                Duty@</label>
                                        </td>

                                        <td width="20%" height="10%" align="center">
                                            <select name="cus_duty" id="cus_duty" class="form-control height_style" style="width: 50%">
                                                <option value="">10%</option>
                                                <option value="">20%</option>
                                            </select>
                                        </td>

                                        <td height="20%" width="20%" align="right">
                                            <label id="customs_duty_amount"
                                                style="display: none; width: 200px; text-align: right; font-size: 16px;">
                                                <b>99,625</b>
                                            </label>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td height="20%" align="left" style="padding:1px 10px;">
                                            <input type="checkbox" id="social_checkbox" name="social_checkbox"
                                                style="margin-right: 5px;">
                                            <label class="form-label" for="social_checkbox">Social Welfare
                                                Cess</label>
                                        </td>

                                        <td width="20%"  height="10%" align="center">
                                            <select name="cus_duty" id="cus_duty" class="form-control height_style" style="width: 50%">
                                                <option value="">10%</option>
                                            </select>
                                        </td>

                                        <td height="20%" width="20%" align="right">
                                            <label id="social_welfare_amount"
                                                style="display: none; width: 200px; text-align: right; font-size: 16px; font-weight: bold;">
                                                9,962
                                            </label>
                                        </td>
                                    </tr>
                                                                                                                                                                                                                                                                                  <tr> 
                                    <tr>
                                        <!-- GST Type Select + Checkbox -->
                                        <td height="20%" align="left" style="padding:1px 10px; ">
                                            <input type="checkbox" id="tax_checkbox" style="margin-right: 5px;">
                                            <select class="form-select height_style" style="width: 40%; display: inline-block;" name=""
                                                id="">
                                                <option value="">GST</option>
                                                <option value="">IGST</option>
                                            </select>
                                        </td>

                                        <!-- Tax % Dropdown -->
                                        <td width="20%" align="center">
                                            <select name="other_tax" id="other_tax" class="form-control height_style" style="width: 50%"
                                                onchange="total_amount_calculation()">
                                                <option value="">5%</option>
                                                <option value="">18%</option>
                                                <option value="">25%</option>
                                            </select>
                                        </td>

                                        <!-- Tax Amount Label (to be toggled) -->
                                        <td height="20%" width="20%" align="right">
                                            <label id="tax_amount"
                                                style="display: none; width: 200px; text-align: right; font-size: 16px; font-weight: bold;">
                                                1,79,325
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20%" align="left" style="padding:1px 10px; padding-top:5px  ">
                                            <input type="checkbox" id="Freight_checkbox" name="Freight_checkbox"
                                                style="margin-right: 5px;">
                                            <label class="form-label" for="Freight_checkbox">Freight Charges</label>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td height="20%" width="20%" align="right">
                                            <input type="text" name="freight_amount" class="form-control height_style"
                                                id="freight_amount" style="text-align:right; width: 200px; display: none;"
                                                value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20%" align="left" style="padding:1px 10px;">
                                            <input type="checkbox" id="OtherCharges_checkbox" name="OtherCharges_checkbox"
                                                style="margin-right: 5px;">
                                            <label class="form-label" for="OtherCharges_checkbox">Other
                                                Charges</label>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td height="20%" width="20%" align="right">
                                            <input type="text" name="other_charges_percentage" class="form-control height_style"
                                                id="other_charges_percentage"
                                                style="text-align:right; width: 200px; display: none;" value="0">
                                        </td>
                                    </tr>

                                    <tr class="mt-2">
                                        <td>
                                            <!-- <label class="form-label" style="margin-left:100px">Net Amount</label> -->
                                        </td>
                                        <td height="20%" width="20%" align="left">
                                            <label class="form-label" style="margin-left:100px">Net Amount</label>
                                        </td>
                                        <td height="20%" width="20%" align="right">

                                            <label id="tax_amount"
                                                style="display: block; width: 200px; text-align: right;font-size: 19px; font-weight:bold">
                                                2,88,912</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <!-- GRN Inward Status -->
                            <!-- <div class="col-md-3">
                                                                                                                                                                                                                                                                <label for="grn_status"><strong>GRN Inward Status</strong></label>
                                                                                                                                                                                                                                                                <select class="form-select" id="grn_status">
                                                                                                                                                                                                                                                                    <option value="Pending">Pending</option>
                                                                                                                                                                                                                                                                    <option value="Completed">Completed</option>
                                                                                                                                                                                                                                                                </select>
                                                                                                                                                                                                                                                            </div> -->

                            <!-- GRN Completed Date (Hidden by default) -->
                            <div class="col-md-3" id="grn_date_div" style="display: none;">
                                <label for="tcs_amount"><strong>Date</strong></label>
                                <input type="date" name="tcs_amount" class="form-control" readonly id="tcs_amount">
                            </div>
                        </div>
                        <div class="btn-float">
                            <button type="button" class="btn btn-secondary me-2" onclick="prevTab(0)">Back</button>
                            <!-- <button type="button" class="btn btn-primary float-end" onclick="nextTab(2)">Next</button> -->
                            <button type="button" class="btn btn-danger me-1"
                                onclick="window.location.href='{{ route('invoice') }}'">Cancel</button>
                            <button type="button" class="btn btn-primary float-end">Save</button>


                        </div>
                    </div>













                </div>
            </form>
        </div>
    </div>



    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




    <!-- Optional: Bootstrap (optional for styling only) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/feather-icons"></script>

    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>
        document.getElementById('customs_duty_checkbox').addEventListener('change', function () {
            const amountLabel = document.getElementById('customs_duty_amount');
            if (this.checked) {
                amountLabel.style.display = 'block';
            } else {
                amountLabel.style.display = 'none';
            }
        });
        document.getElementById('social_checkbox').addEventListener('change', function () {
            const label = document.getElementById('social_welfare_amount');
            label.style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('Freight_checkbox').addEventListener('change', function () {
            const freightInput = document.getElementById('freight_amount');
            freightInput.style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('OtherCharges_checkbox').addEventListener('change', function () {
            const inputField = document.getElementById('other_charges_percentage');
            inputField.style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('tax_checkbox').addEventListener('change', function () {
            const taxAmount = document.getElementById('tax_amount');
            taxAmount.style.display = this.checked ? 'block' : 'none';
        });
        feather.replace()

        $(document).ready(function () {



            showSupplierDetails();
        });

        $('#quotation_id').on('change', function () {


            var quotation_id = $('#quotation_id').val();


            if (quotation_id) {

                $('#details').show();
            } else {
                $('#details').hide();
            }
        });




        function get_bankdetails() {
            var payment_type = $('#pay_type').val();

            var bank_details = document.getElementById('bankdetails');

            if (payment_type == 'NEFT') {
                bank_details.style.display = 'block';

            } else {
                bank_details.style.display = 'none';
            }
        }
        document.getElementById('grn_status').addEventListener('change', function () {
            const status = this.value;
            const dateDiv = document.getElementById('grn_date_div');
            if (status === 'Completed') {
                dateDiv.style.display = 'block';
            } else {
                dateDiv.style.display = 'none';
            }
        });

        $(document).ready(function () {


            $('#location_form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Validate the form
                let isValid = validateForm();
                if (!isValid) {
                    return false;
                }

                // Gather form data
                var formData = {
                    location_name: $('#location_name').val(),
                    type: $('#type').val(),
                    description: $('#description').val(),
                    _token: '{{ csrf_token() }}'
                };


                // Determine method and URL based on whether $acc_year is set
                var method = "{{ isset($location) ? 'PUT' : 'POST' }}";
                var url = "{{ isset($location) ? route('location.update', $location->unique_id) : route('location.store') }}";


                $.ajax({
                    url: url,
                    method: method,
                    data: formData,

                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Location ' + (method === 'POST' ? 'added' :
                                'updated') +
                                ' successfully!',
                            icon: 'success',
                            timer: 1500, // Time in milliseconds (1500ms = 1.5 seconds)
                            showConfirmButton: true, // Hide the confirm button

                        }).then(() => {
                            window.location.href = "{{ route('location') }}";
                        });
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        if (xhr.status === 422) { // Validation error status code
                            var errors = xhr.responseJSON.errors;
                            if (errors.center_name) {
                                Swal.fire(
                                    'Warning',
                                    'Location already exists.',
                                    'warning'
                                );
                            } else {
                                // Handle other validation errors
                                Swal.fire(
                                    'Warning',
                                    'Please check the form for errors.',
                                    'warning'
                                );
                            }
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred. Please try again.',
                                'error'
                            );
                        }
                    }
                });
            });
        });

        function validateForm() {
            let isValid = true;
            let location_name = document.getElementById('location_name');
            let type = document.getElementById('type');

            if (location_name.value == '' || type.value == '') {
                Swal.fire(
                    'Error!',
                    'Fill the mandatory fileds',
                    'error'
                );
                // accerr.textContent = 'center name must not me empty';
                isValid = false;
            }

            return isValid;
        }

        function showSupplierDetails() {
            // alert('test');
            const selectedValue = document.getElementById('quotation_id').value;
            const detailsDiv = document.getElementById('details');

            if (selectedValue !== "") {
                detailsDiv.style.display = 'block';
            } else {
                detailsDiv.style.display = 'none';
            }
        }

        function toggleDivVisibility() {
            const checkbox = document.getElementById('toggleCheckbox');
            const location_name_div = document.getElementById('location_name_div');


            if (checkbox.checked) {
                location_name_div.style.display = 'flex';
            } else {
                location_name_div.style.display = 'none';
                document.getElementById('location_name').value = '';
                showLocationDetails();
            }
        }

        function showBillingDetails() {
            const branchSelect = document.getElementById('branch_name');
            const detailsDiv = document.getElementById('bill_details');
            // const checkBoxDiv = document.getElementById('check-box-div');

            if (branchSelect && detailsDiv) {
                const selectedValue = branchSelect.value.trim();

                if (selectedValue) {
                    detailsDiv.style.display = 'inline-flex';
                } else {
                    detailsDiv.style.display = 'none';
                    uncheckCheckbox(); // Call to uncheck the checkbox
                }
            } else {
                console.warn('Element not found: #branch_name or #bill_details');
            }
        }

        function showLocationDetails() {
            const locationSelect = document.getElementById('location_name');
            const detailsDiv = document.getElementById('location_details');

            if (locationSelect && detailsDiv) {
                const selectedValue = locationSelect.value.trim();
                detailsDiv.style.display = selectedValue ? 'inline-flex' : 'none';
            } else {
                console.warn('Element not found: #location_name or #location_details');
            }
        }

        function nextTab(index) {
            // Hide Tab 1
            document.querySelector('#tab1').classList.remove('show', 'active');

            // Show Tab 2
            document.querySelector('#tab2').classList.add('show', 'active');

        }

        function prevTab(index) {
            // Hide Tab 1
            document.querySelector('#tab1').classList.add('show', 'active');

            // Show Tab 2
            document.querySelector('#tab2').classList.remove('show', 'active');

        }

        function uncheckCheckbox() {
            const checkbox = document.getElementById('toggleCheckbox');
            checkbox.checked = false;
            toggleDivVisibility(); // Ensure the div is hidden
        }
    </script>

@endsection