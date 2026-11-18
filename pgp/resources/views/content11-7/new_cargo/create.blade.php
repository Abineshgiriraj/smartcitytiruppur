@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Registration/</span>Cargo/Container</h4>
    </div>
</div>

<style>
/* btn style */
.btn-right {
    float: right;
}

.lab h4 {
    font-size: 14px;
}
h4{
    font-weight: 700;
    font-size: 13px;
}
.cargo-details{
    height: 24px;
}
hr{
    margin-top: 14px;
    height: 3px;
    background-color: #5859ae;
}
</style>

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="cargo_container">Cargo/Container</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select me-" name="cargo_container" id="cargo_container"
                                onchange="cargo_container_type()">
                                <option value="">Select</option>
                                <option value="Cargo">Cargo</option>
                                <option value="Container">Container</option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-3"> <label class="form-label me-2"
                                for="transaction_type">Transaction</label></div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select me-" name="transaction_type" id="transaction_type"
                                onchange="updateLabel()">
                                <option value="">Select</option>
                                <option value="export">Export</option>
                                <option value="import">Import</option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" id="dynamic_import" for="category_type"
                                style="display:none"></label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control me-4" id="import_no" name="import_no" value=""
                                style="display:none" />
                        </div>
                        <div class="col-sm-2 mt-3 " style="display:none" id="import_name_label"
                            name="import_name_label">
                            <!-- <label class="form-label md-2" style="display:none" id="import_name_label" name="import_name_label"></label> -->
                        </div>
                        <div class="col-sm-4 mt-3">
                            <button type="button" id="import_name" name="import_name"
                                style="display:none; margin-left: -4px; margin-bottom: -47px;" class="btn btn-danger"
                                onclick="get_data();">
                                GO
                            </button>
                        </div>

                        <!-- export -->
                        <div class="col-sm-4 ">
                            <label class="form-label md-2" id="dynamic_export" name="dynamic_export" for="export_name"
                                style="display:none"></label>
                        </div>
                        <div class="col-sm-4 ">
                            <input type="text" class="form-control md-4" id="export_no" name="export_no" value=""
                                style="display:none;" />
                        </div>
                        <div class=" col-sm-4 mt-3">
                            <button type="button" id="import_name" name="import_name"
                                style="display:none; margin-left: -4px; margin-bottom: -47px;" class="btn btn-danger"
                                onclick="get_data();">
                                GO
                            </button>
                        </div>
                        <!-- </div> -->
                    </div>
                    <!-- get details in cargo -->
                    <div class="row mb-3" style="display:none" id="cargo_details">
                        <div class="row cargo-details" >
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="veh_status_label" style="font-size:12px;">Vehicle
                                        Status</label>
                            </div>
                            <div class="col-sm-2 lab cargo-details">
                                <label class="form-label me-2" id="veh_status" name="veh_status">
                                    <h4>FDFGD</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="veh_status" name="veh_status" -->
                                <!-- value="" /> -->
                            </div>

                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="cha_name_label" style="font-size:12px;">CHA
                                        Name</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="cha_name" name="cha_name" value="" /> -->
                                <label class="form-label me-2 lab" id="cha_name" name="cha_name">
                                    <h4>DEMO</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="vessel_no_label" style="font-size:12px;">Vessel
                                        No</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" id="vessel_no" name="vessel_no">
                                    <h4>V54354</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="vessel_no" name="vessel_no" value="" /> -->
                            </div>
                        </div>
                        <div class="row cargo-details">


                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="vessel_name_label" style="font-size:12px;">Vessel
                                        Name</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="vessel_name" name="vessel_name"
                                    value="" /> -->
                                <label class="form-label me-2" id="vessel_name" name="vessel_name">
                                    <h4>VTEST</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="from_location_label" style="font-size:12px;">From
                                        Location</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" id="from_location" name="from_location">
                                    <h4>ERODE</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="from_location" name="from_location"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="to_location_label" style="font-size:12px;">
                                    To Location
                                </label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="to_location" name="to_location"
                                    value="" /> -->
                                <label class="form-label me-2" id="to_location" name="to_location">
                                    <h4>CBE</h4>
                                </label>
                            </div>
                        </div>

                        <div class="row cargo-details">


                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="fill_qty_tons_label"
                                    style="font-size:12px;">Filled Qty (In
                                        Tons)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" id="fill_qty_tons" name="fill_qty_tons">
                                    <h4>1 TONS</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="fill_qty_tons" name="fill_qty_tons"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="deleiverd_qty_tons_label"
                                    style="font-size:12px;">Deliverd Qty (In
                                        Tons)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="deleiverd_qty_tons"
                                    name="deleiverd_qty_tons" value="" /> -->
                                <label class="form-label me-2" id="deleiverd_qty_tons" name="deleiverd_qty_tons">
                                    <h4>1
                                        TONS</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="bal_qty_tons_label"
                                    style="font-size:12px;">Blanced Qty (In
                                        Tons)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" id="bal_qty_tons" name="bal_qty_tons">
                                    <h4>1 TONS</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="bal_qty_tons" name="bal_qty_tons"
                                    value="" /> -->
                            </div>
                        </div>
                        <div class="row cargo-details">


                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="fill_qty_kg_label" style="font-size:12px;">Filled
                                        Qty (In
                                        Pkgs)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="fill_qty_kg" name="fill_qty_kg"
                                    value="" /> -->
                                <label class="form-label me-2" id="fill_qty_kg" name="fill_qty_kg">
                                    <h4>500KGS</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="deleiverd_qty_kg_label"
                                    style="font-size:12px;">Deliverd Qty (In
                                        Pkgs)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" id="deleiverd_qty_kg" name="deleiverd_qty_kg">
                                    <h4>100
                                        Pkgs</h4>
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="deleiverd_qty_kg"
                                    name="deleiverd_qty_kg" value="" /> -->
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="bal_qty_kg_label" style="font-size:12px;">Blanced
                                        Qty (In
                                        Pkgs)</label>
                            </div>
                            <div class="col-sm-2 cargo-details">
                                <!-- <input type="text" class="form-control me-4" id="bal_qty_kg" name="bal_qty_kg"
                                    value="" /> -->
                                <label class="form-label me-2" id="bal_qty_kg" name="bal_qty_kg">
                                    <h4>50</h4>
                                </label>
                            </div>
                        </div>
                        <div class="row cargo-details">
                            <div class="col-sm-2 cargo-details">
                                <label class="form-label me-2" for="ship_bill_no_label"
                                    style="font-size:12px;">Shipping Bill No</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="ship_bill_no" name="ship_bill_no"> -->
                                <label class="form-label me-2" id="ship_bill_no" name="ship_bill_no">
                                    <h4>ShiP74747</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="ship_bill_date_label"
                                    style="font-size:12px;">Shipping Bill
                                        Date</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="ship_bill_date" name="ship_bill_date"
                                    value="" /> -->
                                <label class="form-label me-2" id="ship_bill_date" name="ship_bill_date">
                                    <h4>23-10-2024</h4>
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="cargo_label"
                                    style="font-size:12px;">Cargo</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="cargo" name="cargo"> -->
                                <label class="form-label me-2" id="cargo" name="cargo">demo</label>
                            </div>
                        </div>

                        <div class="row cargo-details">
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="con_name_label" style="font-size:12px;">Consignee
                                        Name</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="con_name" name="con_name">PRABU</label>
                                <!-- <input type="text" class="form-control me-4" id="con_name" name="con_name" value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="org_destination_label"
                                    style="font-size:12px;">Port of
                                        Origin/Destination
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="org_destination" name="org_destination"
                                    value="" /> -->
                                <label class="form-label me-2" id="org_destination" name="org_destination">FDFGD</label>
                            </div>
                        </div>

                        <hr>
                        <div class="row">

                            <div class="col-sm-2">
                                <label class="form-label me-2" for="veh_no_label" style="font-size:12px;">Vehicle
                                        No</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control me-4" id="veh_no" name="veh_no"
                                    value="vech65456" />
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label me-2" for="total_qty_tons_label"
                                    style="font-size:12px;">Total Qty (In
                                        Tons)</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control me-4" id="total_qty_tons" name="total_qty_tons"
                                    value="" style="padding: 5px;" />
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-1" for="no_of_pack" style="font-size:12px;">No of
                                        Packages</label>
                            </div>
                            <div class="col-sm-2 ">
                                <input type="text" class="form-control me-4" id="no_of_pack" name="no_of_pack"
                                    value="5" />
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-1" for="remark_label"
                                    style="font-size:12px;">Remark</label>
                            </div>
                            <div class="col-sm-2">
                                <textarea type="text" class="form-control me-4" id="remark" name="remark"></textarea>
                            </div>

                        </div>
                    </div>

                    <!-- get details in container -->
                    <div class="row" style="display:none" id="container_details">
                        <div class="row ">
                            <div class="col-sm-2">
                                <label class="form-label me-2" for="liner_label" style="font-size:12px;">Liner
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="liner" name="liner" value="" /> -->
                                <label class="form-label me-2" id="liner" name="liner"> fdf</label>
                            </div>

                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="icd_cfs_fcs"
                                    style="font-size:12px;">ICD/CFS/FCS</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="icd_cfs_fcs" name="icd_cfs_fcs"
                                    value="" /> -->
                                <label class="form-label me-2" id="icd_cfs_fcs" name="icd_cfs_fcs">fdgf</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="port_of_orgin_label" style="font-size:12px;">Port
                                        OF
                                        Orgin</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="port_of_orgin" name="port_of_orgin"
                                    value="" /> -->
                                <label class="form-label me-2" id="port_of_orgin" name="port_of_orgin">fdgf</label>
                            </div>
                        </div>
                        <div class="row ">


                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="to_location_label" style="font-size:12px;">To
                                        Location</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="to_location" name="to_location"
                                    value="" /> -->
                                <label class="form-label me-2" id="to_location" name="to_location">chennai</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="container_status_label"
                                    style="font-size:12px;">Container Status</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="container_status"
                                    name="container_status" value="" /> -->
                                <label class="form-label me-2" id="container_status" name="container_status">yes</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="trans" style="font-size:12px;">Imp/Exp/Trans
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="trans" name="trans">dfsfs
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="trans" name="trans" value="" /> -->
                            </div>
                        </div>

                        <div class="row ">

                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="vessel_name_label" style="font-size:12px;">Vessel
                                        Name</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="vessel_name" name="vessel_name">fds</label>
                                <!-- <input type="text" class="form-control me-4" id="vessel_name" name="vessel_name"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="shipper"
                                    style="font-size:12px;">Shipper</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="shipper" name="shipper">harbour</label>
                                <!-- <input type="text" class="form-control me-4" id="shipper" name="shipper" value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="fill_qty_kg_label" style="font-size:12px;">Voyage
                                        No</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="voyage_no" name="voyage_no">56465</label>
                                <!-- <input type="text" class="form-control me-4" id="voyage_no" name="voyage_no" value="" /> -->
                            </div>
                        </div>
                        <div class="row ">


                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="org_destination_label"
                                    style="font-size:12px;">Port
                                        of
                                        Origin/Destination</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="org_destination" name="org_destination">fdgf</label>
                                <!-- <input type="text" class="form-control me-4" id="org_destination" name="org_destination"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="container_no_label"
                                    style="font-size:12px;">Container NO
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="container_no" name="container_no">6465546
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="container_no" name="container_no"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="bal_qty_kg_label" style="font-size:12px;">CH
                                        Agent
                                        Name</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="ch_agent_name" name="ch_agent_name">ffdsf</label>
                                <!-- <input type="text" class="form-control me-4" id="ch_agent_name" name="ch_agent_name"
                                    value="" /> -->
                            </div>
                        </div>
                        <div class="row ">


                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="cargo_label"
                                    style="font-size:12px;">Cargo</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="cargo" name="cargo"> -->
                                <label class="form-label me-2" id="cargo" name="cargo">ffdg</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="container_size_label"
                                    style="font-size:12px;">Container Size</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="container_size" name="container_size">50</label>
                                <!-- <input type="text" class="form-control me-4" id="container_size" name="container_size"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="cfs_name_label" style="font-size:12px;">CFS
                                        Name</label>
                            </div>
                            <div class="col-sm-2 ">
                                <!-- <input type="text" class="form-control me-4" id="cfs_name" name="cfs_name"> -->
                                <label class="form-label me-2" id="cfs_name" name="cfs_name">fdd</label>
                            </div>
                        </div>
                        <div class="row ">

                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="terminal_name_label"
                                    style="font-size:12px;">Terminal Name</label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="terminal_name" name="terminal_name">gdgf
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="terminal_name" name="terminal_name"
                                    value="" /> -->
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="quality_label" style="font-size:12px;">Quality(in
                                        KG)
                                </label>
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" id="quality" name="quality">67kg
                                </label>
                                <!-- <input type="text" class="form-control me-4" id="quality" name="quality" value="" /> -->
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="veh_no_label" style="font-size:12px;">Vehicle
                                        No</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control me-4" id="veh_no" name="veh_no" value="" />
                            </div>
                            <div class="col-sm-2 ">
                                <label class="form-label me-2" for="remark_label"
                                    style="font-size:12px;">Remark</label>
                            </div>
                            <div class="col-sm-4 ">
                                <textarea type="text" class="form-control me-4" id="remark" name="remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/new_cargo')}}'">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="submit_form()">Submit</button>
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
function submit_form() {
    event.preventDefault();
    var transaction_type = $("#transaction_type").val();
    var exp_imp_no = $("#exp_imp_no").val();
    var exp_imp_name = $("#exp_imp_name").val();
    var address = $("#address").val();
    var cargo_name = $("#cargo_name").val();
    var package_qty = $("#package_qty").val();
    var total_weight = $("#total_weight").val();
    var vessel_name = $("#vessel_name").val();
    var vessel_sr_no = $("#vessel_sr_no").val();
    var agent_type = $("#agent_type").val();
    var agent_details = $("#agent_details").val();
    var org_destination = $("#org_destination").val();


    if ((transaction_type && exp_imp_no && exp_imp_name && address && cargo_name && package_qty && total_weight &&
            vessel_name && vessel_sr_no && agent_type && agent_details && org_destination) != '') {



        var data = {
            transaction_type: transaction_type,
            exp_imp_no: exp_imp_no,
            address: address,
            exp_imp_name: exp_imp_name,
            cargo_name: cargo_name,
            package_qty: package_qty,
            total_weight: total_weight,
            vessel_name: vessel_name,
            vessel_sr_no: vessel_sr_no,
            agent_type: agent_type,
            agent_details: agent_details,
            org_destination: org_destination,

            _token: '{{ csrf_token() }}',
            _method: '{{ isset($cargo_ei) ? "PUT" : "POST"}}'
        };

        let url = '{{ isset($cargo_ei) ? route("cargo_ei.update", $cargo_ei->id) : route("cargo_ei.store") }}';


        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(response) {

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: (data._method === 'POST' ? 'Registered' : 'updated') +
                        ' successfully!',
                }).then(() => {
                    window.location.href = "{{ url('/new_cargo') }}";
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors && errors.usertype) {
                    $('#usertypeError').text(errors.usertype[0]);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errors.usertype[0],
                    });
                }
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Fill Out All Mandatory Fields',
        });
    }
}


// function updateLabel() {
//     const transactionType = document.getElementById('transaction_type').value;
//     alert(transactionType);
//     const dynamicLabel = document.getElementById('dynamic_export');
//     const dynamicLabel_1 = document.getElementById('dynamic_import');

//     if (transactionType === 'export') {
//         dynamicLabel.textContent = "Export(EA) No. ";
//         dynamicLabel_1.textContent = 'Export Name';
//     } else {
//         dynamicLabel.textContent = "Import (IA) No.";
//         dynamicLabel_1.textContent = 'Import Name';
//     }
// }

document.addEventListener('DOMContentLoaded', function() {
    cargo_container_type(); // Call the function on load
    get_data();
});

function cargo_container_type() {
    const cargo_type = document.getElementById('cargo_container').value;
    // alert(cargo_type);
    const transactionType = document.getElementById('transaction_type').value;
    // export name
    const dynamicLabelExport = document.getElementById('dynamic_export');
    const dynamicInputName = document.getElementById('export_no');

    // const Exportname = document.getElementById('export_name_label');
    // const imputexportName = document.getElementById('export_name');


    // import name
    const dynamicLabelImport = document.getElementById('dynamic_import');
    // alert(dynamicLabelImport);
    const dynamicInputNo = document.getElementById('import_no');

    const Importname = document.getElementById('import_name_label');
    // alert(dynamicLabelImport);
    const Inputimportname = document.getElementById('import_name');
    // alert(dynamicInputNo);

    if (cargo_type === 'Cargo' && transactionType === 'export') {
        dynamicLabelExport.textContent = "Export (EA) No.";
        // Show the import label
        dynamicLabelExport.style.display = "block"; // Show the export label
        dynamicInputName.style.display = "block"; // Show the input for Export Name
        dynamicInputNo.style.display = "none"; // Show the input for Export No.
        dynamicLabelImport.style.display = "none";
        // Exportname.style.display = "block";
        // imputexportName.style.display = "block";
        Importname.style.display = "block";
        Inputimportname.style.display = "block";
    } else if (cargo_type === 'Cargo' && transactionType === 'import') {
        dynamicLabelImport.textContent = "Import (IA) No.";
        dynamicLabelImport.style.display = "block"; // Show the import label
        dynamicInputNo.style.display = "block"; // Show the input for Import No.
        dynamicLabelExport.style.display = "none"; // Hide the export label
        dynamicInputName.style.display = "none"; // Hide the input for Export Name
        Importname.style.display = "block";
        Inputimportname.style.display = "block";
        // Exportname.style.display = "none";
        // imputexportName.style.display = "block";
    } else if (cargo_type === 'Container' && transactionType === 'import') {
        dynamicLabelImport.textContent = "Import (EIR) No.";
        dynamicLabelImport.style.display = "block"; // Show the import label
        dynamicInputNo.style.display = "block"; // Show the input for Import No.
        dynamicLabelExport.style.display = "none"; // Hide the export label
        dynamicInputName.style.display = "none"; // Hide the input for Export Name
        Importname.style.display = "block";
        Inputimportname.style.display = "block";
        // Exportname.style.display = "none";
        // imputexportName.style.display = "block";
    } else if (cargo_type === 'Container' && transactionType === 'export') {
        dynamicLabelExport.textContent = "Export (EIR) No.";
        // Show the import label
        dynamicLabelExport.style.display = "block"; // Show the export label
        dynamicInputName.style.display = "block"; // Show the input for Export Name
        dynamicInputNo.style.display = "none"; // Show the input for Export No.
        dynamicLabelImport.style.display = "none";
        // Exportname.style.display = "block";
        // imputexportName.style.display = "block";
        Importname.style.display = "block";
        Inputimportname.style.display = "block";

    }
}

// Ensure the function is called when dropdown values change
document.getElementById('cargo_container').addEventListener('change', cargo_container_type);
document.getElementById('transaction_type').addEventListener('change', cargo_container_type);



function get_data() {
    // alert();
    const cargo_type = document.getElementById('cargo_container').value;
    const transactionType = document.getElementById('transaction_type').value;
    const cargoDetails = document.getElementById('cargo_details');

    const container_details = document.getElementById(
        'container_details'); // Reset visibility by hiding the cargo details initially
    cargoDetails.style.display = "none";
    container_details.style.display = "none";

    // Show cargo details based on the selected options
    if (cargo_type === 'Cargo' && transactionType === 'export') {
        cargoDetails.style.display = "block"; // Show cargo details for Cargo Export
        container_details.style.display = "none";
    } else if (cargo_type === 'Cargo' && transactionType === 'import') {
        cargoDetails.style.display = "block"; // Show cargo details for Cargo Import
        container_details.style.display = "none";
    } else if (cargo_type === 'Container' && transactionType === 'import') {
        container_details.style.display = "block";
        cargoDetails.style.display = "none";

    } else if (cargo_type === 'Container' && transactionType === 'export') {
        container_details.style.display = "block";
        cargoDetails.style.display = "none";


    }



    // No need to show cargo details for Container, as it remains hidden
}
</script>

@endsection