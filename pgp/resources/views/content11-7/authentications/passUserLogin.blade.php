@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('public/assets/vendor/css/pages/page-auth.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css">

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

. span {
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
    padding: 30px 71px 0px;
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
</style>

@php
$get_centers = get_centers();
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

    <div class="col-4 login-page2 align-self-center">
        <!-- <img src="{{asset('https://lobox.com/landing-assets/helpCenter.26865779.svg')}}" class="  " alt="View Sales"> -->

    </div>
    <div class="col-4">
    <img src="{{asset('https://lobox.com/landing-assets/helpCenter.26865779.svg')}}" class="  " alt="View Sales">
        <div class="pass-box">
            <h6 class="mb-1 headtitl"><span>H</span>arbour <span>E</span>ntry <span>P</span>ermit</h6>
            <p class="mb-6">Please log-in to your account </p>

            <form id="formAuthentication" class=" fv-plugins-bootstrap5 fv-plugins-framework"
                action="{{url('/userlogin')}}" method="get" novalidate="novalidate">
                @csrf
                <div class="mb-2 fv-plugins-icon-container">
                    <label for="email" class="form-label"> Issue Center Name</label>
                    <select class="form-select" id="centerName" name="centerName" required>
                        <option value="" selected>Select Center</option>
                        @foreach ($get_centers as $get_center)
                        <option value="{{$get_center->unique_id}}">{{$get_center->center_name}}
                        </option>
                        @endforeach
                    </select>
                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                    </div>
                </div>
                <div class="mb-2 fv-plugins-icon-container">
                    <label for="email" class="form-label"> Username</label>
                    <input type="text" class="form-control" id="user_name" name="user_name"
                        placeholder="Enter your username" autofocus="" required>
                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                    </div>
                </div>
                <div class="mb-6 form-password-toggle fv-plugins-icon-container">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge has-validation">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="············" aria-describedby="password" required>
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>  
                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                    </div>
                    <button class="btn btn-primary d-grid w-100 mt-4">
                    Login in
                </button>
                    
                  
                </div>
                <div class="mt-2">
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



            <!-- <div class="loginAccess">
                <a href="{{ route('home.index') }}">Back to Main Login</a>
            </div> -->

        </div>
    </div>
    <div class="col-4 "></div>

</div>
</div>



<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.all.min.js"></script>

<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{session('
        success ')}}',
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