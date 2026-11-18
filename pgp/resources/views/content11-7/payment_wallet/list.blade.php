@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - User Type Creation')

@section('content')
<style>
.btn-small {
    /* width: auto; */

    padding: 0;
    /* Remove padding */
    margin: 0;
    display: inline-flex;
    /* Align content properly */
    align-items: center;
    width: 70px;
    /* Center icons and text vertically */
    /* Remove margin */
}

.lab_add {
    margin-left: 0.5rem;
    /* Add space between icon and text */
    white-space: nowrap;
    /* Prevent text from wrapping */
    font-size: 10px;
}
</style>
<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Authentication /</span> Payment Wallet
        </h4>
    </div>

</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table id="usertypeTable" class="table table-bordered dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>S.No</th> <!-- Added S.No column -->
                        <th>Entry Date</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Company Name</th>
                        <th>Authenticated By</th>
                        <th>Renewal Date</th>
                        <th>Expire Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td> <!-- Added S.No column -->
                        <td>21.09.2024</td>
                        <td>ID-5674</td>
                        <td style="cursor: pointer;">Harish</td>
                        <td>Company 1</td>
                        <td>Shankar</td>
                        <td>01.03.2023</td>
                        <td>01.03.2025</td>
                        <td>
                            <button class="btn btn-primary btn-right btn-small" type="button"
                                onclick="window.location='{{ route('payment_option') }}'">
                                <i class="i-Add"></i><span class="lab_add">Pay Now</span>
                            </button>

                        </td>
                    </tr>
                    <tr>
                        <td>2</td> <!-- Added S.No column -->
                        <td>ID-7865</td>
                        <td>ID-7865</td>
                        <td style="cursor: pointer;">Rohit</td>
                        <td>Company 2</td>
                        <td>Muthu</td>
                        <td>12.03.2024</td>
                        <td>01.08.2032</td>
                        <td>
                            <button class="btn btn-primary btn-right btn-small" type="button"
                                onclick="window.location='{{ route('payment_option') }}'">
                                <i class="i-Add"></i> <span class="lab_add">Pay Now</span>
                            </button>

                        </td>
                    </tr>
                </tbody>
            </table>
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


<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


@endsection