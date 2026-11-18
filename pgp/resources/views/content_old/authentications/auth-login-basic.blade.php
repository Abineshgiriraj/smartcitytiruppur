@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('public/assets/vendor/css/pages/page-auth.css')}}">
    <!-- SweetAlert CSS -->
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css"> -->
@endsection

@section('content')
    <style>
        body {
            background: #ffffff !important;
        }
.btn-primary {
    background-color: #85c440 !important;
    border-color: #85c440 !important;}
.btn-primary:active{
    background-color: #002e5e !important;
    border-color: #002e5e !important;}
.btn-primary:hover{
    background-color: #002e5e !important;
    border-color: #002e5e !important;}
.br-ra {
            margin: 0px auto;
    text-align: center;
    margin-bottom: 29px;
        }

        .headtitl {
            font-size: 24px;
            text-align: center;
        }

        .log-box p {
            text-align: center;
            color: #85c440;
        }

        .headtitl span {
            color: #7374ec;
        }

        .w-px-400 {
            width: 470px !important;
        }

        .padd1 {
            padding: 10%;
        }

        .span {
            color: #5cb8fc;
            font-size: 30px;
        }

        .loginAccess {
            text-align: end;
        }

        .btns {
            height: 40px;
        }

        .log-box {
            border: 1px dashed #002e5e;
            padding: 27px 37px;
            border-radius: 5px;
            margin-top: 60px;
            background: #ffffff;
        }

        .w-px-400 {

            height: fit-content;
        }

        span.User a {
            font-size: 18px;
        }

        .login-hed {
            padding: 30px 71px;
        }

        body {
           background-image: url('http://103.110.236.187/urban_erp/public/assets/img/login-bgg.jpg') !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            overflow-x: hidden;
        }
		

        .login-hed h5 {
            font-size: 34px;
            color: #8874ec;
            font-weight: 600;
            font-family: 'Poppins';
        }

        span.User a:hover {
            background: #8874ec;
            color: #fff;
        }

        span.User a {
            font-size: 18px;
            border: 1px solid #8874ec;
            padding: 5px 9px;
            margin-right: 20px;
            border-radius: 5px;
        }

        button.btn.btn-primary {
            font-size: 14px;
            padding: 9px 7px;
        }

        p.top {
            color: #566a7f;
            text-align: justify;
        }

        .modal-simple .btn-close {
            position: absolute;
            top: 1rem;
        }

        .modal-simple .modal-content {
            padding: 3rem;
            padding-top: 0px;
            padding-bottom: 0px;
        }
		img.login-page2 {
    width: 30%;
}
    </style>
    <!--<div class="row login-hed">
        <div class="col-6">
            <h5>Ascent Urban - ERPsds</h5>
        </div>

    </div>--->
    <div class=" row m-0 ">
        <!-- <div class="d-none d-lg-flex col-lg-4 col-xl-5 align-items-center p-5 pb-0 br-ra">
                <img src="{{asset('public/assets/img/login-4.jpg')}}" class="login-page" alt="View Sales"> 
            </div> -->
        <div class=" col-3 "></div>
        <div class=" col-6  ">
            <div class="w-px-400 mx-auto mt-12 log-box ">
			<div class=" align-items-center pb-0 br-ra">
                <img src="{{asset('public/assets/img/up.png')}}" class="login-page2" alt="View Sales"> 
            </div>
                <h6 class="mb-1 headtitl">Ascent Urban - ERP</h6>
                <p class="mb-6">Please log-in to your account</p>

                <form id="formAuthentication" class="mb-6 fv-plugins-bootstrap5 fv-plugins-framework"
                    action="{{url('/login')}}" method="get" novalidate="novalidate">
                    @csrf
                    <div class="mb-6 fv-plugins-icon-container">
                        <label for="user_name" class="form-label">Username</label>
                        <input type="text" class="form-control" id="user_name" name="user_name"
                            placeholder="Enter your username" autofocus="">
                        <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                        </div>
                    </div>
                    <div class="mb-6 form-password-toggle fv-plugins-icon-container">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge has-validation">
                            <input type="password" id="password" class="form-control" name="password"
                                aria-describedby="password">
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                        </div>
                    </div>

                    <button class="btn btn-primary d-grid w-100 btns">
                        Login
                    </button>

                </form>
                <div class="row ">
                    <a href="" class="" data-bs-toggle="modal" data-bs-target="#exampleModal" style="text-align: center;">
                        Forgot Password</a>
                </div>

                <!-- Button trigger modal -->

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                <div class="text-center mb-6">
                                    <img src="{{asset('public/assets/img/Forgot password-cuate.svg')}}" class="  "
                                        alt="View Sales" style="width: 37%;">
                                    <h4 class="mb-2">Forgot your Password?</h4>


                                </div>

                                <div class="col-12 ">
                                    <p class="top">Enter your mobile phone number and we will send you a verification code.
                                    </p>
                                    <label class="form-label" for="modalAddressAddress1">Enter Your Phone Number</label>
                                    <input type="number" id="modalAddressAddress1" name="modalAddressAddress1"
                                        class="form-control">
                                </div>

                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3">Sent Otp</button>
                            </div>

                            <hr>

                            <div class="card-body">
                                <!-- Logo -->

                                <p class="mb-0">Type your 6 digit security code</p>
                                <!-- action="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1" -->
                                <form id="twoStepsForm" action='{{ route("verfication.index") }}' method="GET"
                                    class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                    <div class="mb-6 fv-plugins-icon-container">
                                        <div
                                            class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1" autofocus="">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1">
                                            <input type="tel"
                                                class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                                maxlength="1">
                                        </div>
                                        <!-- Create a hidden field which is combined by 3 fields above -->
                                        <input type="hidden" name="otp">
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary d-grid w-100 mb-6">
                                        Verify my account
                                    </button>

                                    <input type="hidden">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="loginAccess">
                        <span class="User">
                            <a href="{{ route('PassUser.index') }}">Pass Issue Center Login</a> /
                            <a href="{{ route('GateUser.index') }}">Gate Access Login</a>
                        </span>
                    </div> -->
            </div>
        </div>
        <div class=" col-3  "></div>
    </div>


    <!-- SweetAlert JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.all.min.js"></script> -->
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{session('success')}}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Try Again',
                timer: 2000,
                timerProgressBar: true // Optional, shows a progress bar
            });
        @endif
    </script>

@endsection