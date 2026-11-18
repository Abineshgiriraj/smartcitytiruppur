@extends('layouts/blankLayout')

@section('title', 'Staff Login | Smart City Tiruppur')

@section('page-style')
<style>
  body { margin:0; background:#f3f7fb; font-family:'Segoe UI',Arial,sans-serif; }
  .tcmc-login { min-height:100vh; display:grid; grid-template-columns:minmax(320px,480px) minmax(0,1fr); }
  .tcmc-login-form { display:flex; justify-content:center; align-items:center; padding:48px 30px; background:#fff; }
  .tcmc-login-inner { width:100%; max-width:365px; }
  .tcmc-login-logo { display:block; width:76px; height:76px; object-fit:contain; margin:0 auto 24px; }
  .tcmc-login h1 { font-weight:750; text-align:center; color:#173d53; font-size:27px; line-height:1.25; margin:0 0 10px; }
  .tcmc-login-subtitle { text-align:center; color:#657a89; font-size:14px; margin:0 0 32px; }
  .tcmc-login .form-label { display:block; color:#284556; font-size:13px; font-weight:650; margin-bottom:7px; }
  .tcmc-login .form-control { display:block; width:100%; min-height:44px; box-sizing:border-box; border:1px solid #cbd8e2; border-radius:7px; padding:10px 12px; margin-bottom:20px; font-size:14px; background:#fff; color:#162a38; }
  .tcmc-login .form-control:focus { outline:0; border-color:#118cc7; box-shadow:0 0 0 3px rgba(17,140,199,.13); }
  .tcmc-login-submit { display:block; width:100%; padding:13px 20px; border:0; background:#0d7bad; border-radius:7px; color:white; font-weight:700; cursor:pointer; font-size:15px; margin-top:8px; }
  .tcmc-login-submit:hover { background:#075f89; }
  .tcmc-login-errors { background:#fff0ee; border:1px solid #f0b9b2; color:#992d24; border-radius:6px; padding:11px 13px; margin-bottom:20px; font-size:13px; }
  .tcmc-login-side { display:flex; justify-content:center; align-items:center; padding:50px; color:#fff; background:linear-gradient(145deg,#11354f 0%,#075d8c 55%,#128db9 100%); }
  .tcmc-login-side-inner { max-width:510px; }
  .tcmc-login-eyebrow { font-size:13px; text-transform:uppercase; letter-spacing:2.5px; opacity:.85; font-weight:700; }
  .tcmc-login-side h2 { font-size:clamp(30px,3.5vw,48px); line-height:1.15; font-weight:750; margin:18px 0 20px; }
  .tcmc-login-side p { font-size:17px; line-height:1.65; color:#e1f2fc; }
  .tcmc-login-note { text-align:center; font-size:12px; margin-top:24px; color:#8094a1; }
  @media(max-width:850px) { .tcmc-login { grid-template-columns:1fr; } .tcmc-login-side { display:none; } .tcmc-login-form { min-height:100vh; } }
</style>
@endsection

@section('content')
<main class="tcmc-login">
  <section class="tcmc-login-form" aria-label="Municipal staff login">
    <div class="tcmc-login-inner">
      <img src="{{ asset('assets/img/up.png') }}" alt="Municipal emblem" class="tcmc-login-logo"
           onerror="this.style.display='none'">
      <h1>Smart City Tiruppur</h1>
      <p class="tcmc-login-subtitle">Municipal Staff &amp; Grievance Administration</p>
      @if ($errors->any())
        <div class="tcmc-login-errors" role="alert">
          {{ $errors->first('error') ?: $errors->first() }}
        </div>
      @endif
      <form id="formAuthentication" action="{{ url('/login') }}" method="post">
        @csrf
        <label for="user_name" class="form-label">Username</label>
        <input type="text" class="form-control" id="user_name" name="user_name"
               placeholder="Enter your username" autocomplete="username" value="{{ old('user_name') }}" required autofocus>
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" class="form-control" name="password"
               autocomplete="current-password" required>
        <button type="submit" class="tcmc-login-submit">Sign In</button>
      </form>
      <p class="tcmc-login-note">Authorized municipal personnel only</p>
    </div>
  </section>
  <aside class="tcmc-login-side">
    <div class="tcmc-login-side-inner">
      <span class="tcmc-login-eyebrow">Tiruppur City Municipal Corporation</span>
      <h2>One city.<br>Better public service.</h2>
      <p>Manage citizen grievances, assign responsibility, monitor resolutions and improve service delivery in one place.</p>
    </div>
  </aside>
</main>
@endsection
