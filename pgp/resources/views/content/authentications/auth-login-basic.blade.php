@extends('layouts/blankLayout')

@section('title', 'Login - Public Grievance Portal')

@section('page-style')
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #fff;
    }

    .login-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* Left Side - Login Form */
    .login-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 40px;
        max-width: 500px;
        margin: auto;
    }

    .login-left img.logo {
        width: 100px;
        margin-bottom: 20px;
    }

    .login-left h6 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #111;
        text-align: center;
    }

    .login-left p {
        color: #666;
        text-align: center;
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        height: 45px;
        font-size: 14px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
        padding: 0 12px;
    }

    .btn-login {
        background: linear-gradient(90deg, #0f99e4ff, #05689e);
        border: none;
        border-radius: 6px;
        height: 45px;
        color: #fff;
        font-weight: 500;
        font-size: 15px;
        cursor: pointer;
        width: 100%;
        margin-bottom: 16px;
    }

    .login-links {
        display: flex;
        justify-content: center;
        font-size: 13px;
        margin-bottom: 16px;
    }

    .login-links a {
        color: #4f46e5;
        text-decoration: none;
    }

    /* Right Side - Info */
    .login-right {
        flex: 1;
        background-image:url('public/assets/img/login-bg.jpg') ;
      
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        text-align: center;
    }

    .login-right h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .login-right p {
        font-size: 14px;
        margin-bottom: 30px;
        color: #ddd;
    }

    .mockup {
        background: white;
        border-radius: 16px;
        padding: 20px;
        max-width: 350px;
        width: 100%;
    }

    @media (max-width: 900px) {
        .login-wrapper {
            flex-direction: column;
        }
        .login-right {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="login-wrapper">
    <!-- Left Side -->
    <div class="login-left">
        <div style="text-align:center;">
            <img src="{{asset('public/assets/img/up.png')}}" class="logo" alt="Logo">
        </div>
        <h6>Public Grievance Portal</h6>
        <p>Please log-in to your account</p>

        <form id="formAuthentication" action="{{url('/login')}}" method="get">
            @csrf
            <label for="user_name" class="form-label">Username</label>
            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter your username" autofocus>

            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" name="password">

            <div class="login-links">
                <a href="" data-bs-toggle="modal" data-bs-target="#exampleModal">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

    <!-- Right Side -->
    <div class="login-right">
        <h3></h3>
        <p></p>
        <!-- <div class="mockup">
            <h3>This portal ensures accountability across all departments.
All grievances are monitored until satisfactory</h3>
        </div> -->
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Keep your existing modal content here -->
</div>
@endsection
