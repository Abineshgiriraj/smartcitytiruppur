<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;

class Analytics extends Controller
{
    public function index()
    {
        // print_r('session out');die(session('user_id'));
        // $breakfast_absent = biometric_attandance::where('verify_mode', 'BreakfastOn')->count();

        // if (session('user_id') == '') {
        //     // print_r('session out');
        //     // die(session('user_id'));
        //     \Auth::logout();
        //     session()->flush();
        //     return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        // }
\Log::info('SESSION ID:', ['cookie' => request()->cookie('laravel_session')]);


        // dd(session('user_id'));
        return view('content.dashboard.dashboards-analytics');
    }
}