@extends('layouts/contentNavbarLayout')

@section('title', 'Port Issue Centers - List')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Inward Module/</span>Invoice</h4>
            <a href="{{ route('invoice_add') }}" class="btn btn-primary btn-right mb-1">Add New</a>
        </div>
    </div>
    <div class="row list-pad">
        <div class="col-md-3">
            <div class="form-group">
                <label>Purchase No</label>
                <select name="business_type1" id="business_type1" class="select2 form-control">
                    <option value="1">select</option>
                    <option value="1">PO-250730-0001</option>
                    <option value="2">PO-250730-0002</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Invoice No</label>
                <select name="business_type2" id="business_type2" class="select2 form-control">
                    <option value="1">select</option>
                    <option value="1">INV-250730-0001</option>
                    <option value="2">INV-250730-0002</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Status</label>
                <select name="business_type3" id="business_type3" class="select2 form-control">
                    <option value="1">Pending</option>
                    <option value="2">Completed</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 align-self-center">
            <button type="button" class="btn btn-primary  btn-rounded mt-3" onclick="servier_enquiry_filter();">Go</button>

        </div>
    </div>
    <br>
    <!-- Table to display centers -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="d-flex justify-content-end mb-2">
                    search:&nbsp;<input type="text" id="customSearch" class="form-control"
                        style="height:30px; width: 180px;">
                </div>
                <table class="table table-bordered dt-responsive nowrap" id="invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center">S.NO</th>
                            <th class="text-center">Supplier Name</th>
                            <th class="text-center">Quotation No</th>
                            <th class="text-center">Invoice No</th>
                            <th class="text-center">Purchase No</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div style="font-weight: bold; font-size: 16px;">Union Bank Of India</div>
                            <div style="font-size: 14px;">440, car street, Tiruppur-26</div>
                               
                            </td>
                            <td>
                                 <div style="font-weight: bold; font-size: 16px;"> AURQTO-250725-0001</div>
                            <div style="font-size: 14px;">28-07-2025</div>
                               
                            </td>
                            <td>
                                 <div style="font-weight: bold; font-size: 16px;">INV-250730-0001</div>
                            <div style="font-size: 14px;">31-07-2024</div>
                                
                            </td>
                            <td>
                                 <div style="font-weight: bold; font-size: 16px;">PO-250730-0001</div>
                            <div style="font-size: 14px;">31-07-2024</div>
                               
                            </td>
                            <td>Pending</td>
                            <td style="text-align : right">5,00,000</td>
                            <td>
                                <a href="{{ route('q_view.view') }}" class="text-info" title="View"
                                    onclick="openPrintWindow(event, this.href)">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-danger" title="Delete" onclick=""
                                    style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>

        function openPrintWindow(event, url) {
            event.preventDefault(); // Prevent normal redirect

            const printWindow = window.open(url, 'PrintWindow', 'width=1000,height=800');

            // Optionally, auto-trigger print once loaded
            printWindow.onload = function () {
                printWindow.focus();
                printWindow.print();
            };
        }

    </script>

@endsection