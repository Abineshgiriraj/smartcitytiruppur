@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('public/assets/vendor/css/pages/page-auth.css')}}">
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css"> -->

@endsection

@section('content')
<style>
    body {
        background: #ffffff !important;
    }

    .br-ra {
        background: #fff;
        border-radius: 0px 15% 15% 10px;
    }

    .headtitl {
        font-size: 24px;
        text-align: center;
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

    .pass-box {
        border: 1px solid #5859ae;
        padding: 27px 37px;
        border-radius: 5px;

        background: #ffffff;
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

    .login-hed {
        padding: 30px 71px;
    }

    .pass-box p {
        text-align: center;
        color: #8081ec;
    }

    .login-page2 img {
        width: 100%;
    }

    .login-page2 {
        text-align: end;
    }

    body {
        overflow-x: hidden;
    }

    button.btn.btn-primary {
        font-size: 14px;
        padding: 9px 7px;
    }

    .switch-toggle-slider:after {
        background: #ff3e1d;
    }

    .switch-off,
    .switch-on {

        width: 100%;
        COLOR: #000;
        font-weight: 600;
        font-size: 11px;
        padding-top: .50rem;

    }

    .switch .switch-toggle-slider {

        height: 37px;
        width: 3.5rem;
        margin-left: 23px;
    }

    .switch .switch-toggle-slider:after {
        height: 14px;
        margin: .225rem 0 0 .225rem;
        top: 7px;
        width: 14px;
    }

    .switch .switch-input:checked~.switch-toggle-slider:after {
        left: 2.15rem;
    }

    .switch-input:checked~.switch-toggle-slider:after {
        background: #66c732;
    }

    #gate_area {
        width: 81%;
    }

    .accessDiv {
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
    }
</style>

@php
$get_access_gate_area = access_area_gate();
$get_access_gate = get_access_gate();
@endphp


<div class="row login-hed">
    <div class="col-6">
        <h5>VOC PORT - Thoothukudi</h5>
    </div>
    <div class="col-6">
        <div class="loginAccess">
            <span class="User">
                <a href="{{ route('home.index') }}">Back to Main Login</a>
            </span>
        </div>
    </div>
</div>


<div class=" row m-0 ">
    <div class="col-2"></div>
    <div class="col-3 login-page2 align-self-center" style="padding-right: 0px;">
        <img src="{{asset('public/assets/img/bg-001.png')}}" class="  " alt="View Sales">

    </div>
    <div class="col-4" style="padding-left: 0px;">
        <div class="pass-box ">
            <h6 class="mb-1 headtitl"><span>H</span>arbour <span>E</span>ntry <span>P</span>ermit</h6>
            <p class="mb-6">Please log-in to your account </p>


            <form id="formAuthentication" class="fv-plugins-bootstrap5 fv-plugins-framework"
                action="{{url('/gatelogin')}} " method="get" novalidate="novalidate">
                @csrf
                <div class="mb-3 fv-plugins-icon-container accessDiv">
                    <label for="email" class="form-label"> Access Gate Area</label>
                    <select class="form-select" id="gate_area" name="gate_area" required>
                        @foreach ($get_access_gate_area as $access_gate)
                        <option value="{{$access_gate->unique_id}}">{{$access_gate->area_name}}
                        </option>
                        @endforeach
                    </select>
                    <label class="switch switch-square">
    <input type="checkbox" class="switch-input" id="access_gate" name="access_gate">
    <span class="switch-toggle-slider">
        <span class="switch-on" data-value="IN">IN</span>
        <span class="switch-off" data-value="OUT">OUT</span>
    </span>
</label>

<input type="hidden" id="access_gate_value" name="access_gate_value">

                </div>

                <div class="mb-3 fv-plugins-icon-container">
                    <label for="gate" class="form-label">Gate </label>
                    <select class="form-select" id="gate_no" name="gate_no">
                        <option value="">Select Gate</option>
                        <option value="1">Gate 1</option>
                        <option value="2">Gate 2</option>
                        <option value="3">Gate 3</option>
                        <option value="4">Gate 4</option>
                    </select>
                </div>
                <div class="mb-3 fv-plugins-icon-container">
                    <label for="email" class="form-label"> Username</label>
                    <input type="text" class="form-control" id="user_name" name="user_name"
                        placeholder="Enter your username" autofocus="" required>
                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                    </div>
                </div>
                <div class="mb-4 form-password-toggle fv-plugins-icon-container">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge has-validation">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="············" aria-describedby="password" required>
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                    </div>
                </div>

                <button class="btn btn-primary d-grid w-100 btns">
                    Login
                </button>
                <div class="my-2">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <!-- <input class="form-check-input" type="checkbox" id="remember-me">
                            <label class="form-check-label" for="remember-me">
                                Remember Me
                            </label> -->
                        </div>
                        <a href="">
                            <p class="mb-0">Forgot Password?</p>
                        </a>
                    </div>
                </div>
                <!-- <input type="hidden"> -->
            </form>
        </div>
    </div>
    <div class="col-2"></div>

</div>
</div>



<!-- SweetAlert JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.all.min.js"></script> -->
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>

<script>


//    document.getElementById('access_gate').addEventListener('change', function () {
//     const accessGateValue = this.checked ? 'IN' : 'OUT';
//     document.getElementById('access_gate_value').value = accessGateValue;




// document.addEventListener("DOMContentLoaded", function () {
//     let accessGate = document.getElementById('access_gate');

//     // Attach change event listener
//     accessGate.addEventListener('change', function () {
//         const accessGateValue = this.checked ? 'IN' : 'OUT';
//         document.getElementById('access_gate_value').value = accessGateValue;
//         console.log('Change event triggered!', accessGateValue);
//     });

//     // Trigger the change event programmatically
//     accessGate.dispatchEvent(new Event('change'));
// });
// });


$(document).ready(function(){

    let access_gate = $('#access_gate');

    access_gate.on('change',function(){
        const accessGateValue = this.checked ? 'IN' : 'OUT';
        $('#access_gate_value').val(accessGateValue);
        console.log('Change event triggered!', accessGateValue);

    });

    access_gate.trigger('change');
});

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{session("success")}}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });

    @endif

    @if($errors-> any())
    Swal.fire({
        icon: 'error',
        text: '{{ $errors->first() }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Try Again',
        timer: 5000,
        timerProgressBar: true // Optional, shows a progress bar
    });
    @endif
</script>

@endsection