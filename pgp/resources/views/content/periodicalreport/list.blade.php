@extends('layouts/contentNavbarLayout')

@section('title', 'Complaint Tracking System')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Custom button styles */
    .btn-right {
        float: right;
    }
</style>
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4>
            <span class="text-muted fw-light">Report /</span> Periodical Report



        </h4>
    </div>
</div>
<!-- Right-aligned buttons -->
<div class="row align-items-Right">
    <div class="col-md-7">
        <!-- Your filters/search form -->
    </div>
    <div class="col-md-5 d-flex justify-content-end gap-2 flex-nowrap">
        <button id="print_btn" class="btn btn-outline-primary">
            <i class="bi bi-printer"></i> Print
        </button>
        <button id="excel_btn" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top">
                <thead>
                    <tr>
                        <th style="text-align: center;">Zone</th>
                        <th style="text-align: center;">Opening Balance</th>
                        <th style="text-align: center;">During this week</th>
                        <th style="text-align: center;">Total Pending</th>
                        <th style="text-align: center;">Accounts</th>
                        <th style="text-align: center;">Engg-Sewage</th>
                        <th style="text-align: center;">Engg-Street Light</th>
                        <th style="text-align: center;">Engg-Water Supply</th>
                        <th style="text-align: center;">Engg-Works</th>
                        <th style="text-align: center;">Engg-Town Plan</th>
                        <th style="text-align: center;">Public Health</th>
                        <th style="text-align: center;">Revenue</th>
                        <th style="text-align: center;">eGovernance</th>
                        <th style="text-align: center;">Below 30 days</th>
                        <th style="text-align: center;">30-60 days</th>
                        <th style="text-align: center;">60-90 days</th>
                        <th style="text-align: center;">Above 90 days</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">ZONE - 01</td>
                        <td style="text-align: center;">147</td>
                        <td style="text-align: center;">45</td>
                        <td style="text-align: center;">192</td>
                        <td style="text-align: center;">2</td>
                        <td style="text-align: center;">35</td>
                        <td style="text-align: center;">11</td>
                        <td style="text-align: center;">46</td>
                        <td style="text-align: center;">18</td>
                        <td style="text-align: center;">13</td>
                        <td style="text-align: center;">44</td>
                        <td style="text-align: center;">11</td>
                        <td style="text-align: center;">0</td>
                        <td style="text-align: center;">119</td>
                        <td style="text-align: center;">24</td>
                        <td style="text-align: center;">8</td>
                        <td style="text-align: center;">42</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">ZONE - 02</td>
                        <td style="text-align: center;">239</td>
                        <td style="text-align: center;">35</td>
                        <td style="text-align: center;">274</td>
                        <td style="text-align: center;">0</td>
                        <td style="text-align: center;">57</td>
                        <td style="text-align: center;">14</td>
                        <td style="text-align: center;">43</td>
                        <td style="text-align: center;">57</td>
                        <td style="text-align: center;">62</td>
                        <td style="text-align: center;">18</td>
                        <td style="text-align: center;">8</td>
                        <td style="text-align: center;">0</td>
                        <td style="text-align: center;">80</td>
                        <td style="text-align: center;">27</td>
                        <td style="text-align: center;">19</td>
                        <td style="text-align: center;">150</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">ZONE - 03</td>
                        <td style="text-align: center;">440</td>
                        <td style="text-align: center;">60</td>
                        <td style="text-align: center;">500</td>
                        <td style="text-align: center;">3</td>
                        <td style="text-align: center;">143</td>
                        <td style="text-align: center;">2</td>
                        <td style="text-align: center;">84</td>
                        <td style="text-align: center;">158</td>
                        <td style="text-align: center;">50</td>
                        <td style="text-align: center;">28</td>
                        <td style="text-align: center;">2</td>
                        <td style="text-align: center;">189</td>
                        <td style="text-align: center;">79</td>
                        <td style="text-align: center;">57</td>
                        <td style="text-align: center;">188</td>
                        <td style="text-align: center;">-</td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: center;">ZONE - 04</td>
                        <td style="text-align: center;">270</td>
                        <td style="text-align: center;">34</td>
                        <td style="text-align: center;">304</td>
                        <td style="text-align: center;">0</td>
                        <td style="text-align: center;">112</td>
                        <td style="text-align: center;">21</td>
                        <td style="text-align: center;">27</td>
                        <td style="text-align: center;">59</td>
                        <td style="text-align: center;">59</td>
                        <td style="text-align: center;">6</td>
                        <td style="text-align: center;">11</td>
                        <td style="text-align: center;">104</td>
                        <td style="text-align: center;">59</td>
                        <td style="text-align: center;">42</td>
                        <td style="text-align: center;">102</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><strong>Total</strong></td>
                        <td style="text-align: center;">1096</td>
                        <td style="text-align: center;">174</td>
                        <td style="text-align: center;">1270</td>
                        <td style="text-align: center;">5</td>
                        <td style="text-align: center;">347</td>
                        <td style="text-align: center;">48</td>
                        <td style="text-align: center;">200</td>
                        <td style="text-align: center;">292</td>
                        <td style="text-align: center;">184</td>
                        <td style="text-align: center;">96</td>
                        <td style="text-align: center;">32</td>
                        <td style="text-align: center;">492</td>
                        <td style="text-align: center;">189</td>
                        <td style="text-align: center;">126</td>
                        <td style="text-align: center;">482</td>
                        <td style="text-align: center;">192</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Bootstrap Icons & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



@endsection
