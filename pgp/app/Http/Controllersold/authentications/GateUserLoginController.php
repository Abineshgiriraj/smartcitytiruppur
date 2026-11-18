<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccessGate;
use App\Models\PortAreaAccess;
use App\Models\acc_year;
use App\Models\user_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GateUserLoginController extends Controller
{
    public function index()
    {
        return view('content.authentications.gateUserLogin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gate_area' => 'required',
            // 'access_gate' => 'required',
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to find the user by username and password
        $user = User::where('user_name', $request->user_name)->where('password', $request->password)->first();

        // && Hash::check($request->password, $user->password)
        
        if ($user && $user->user_type == '670f52142fb6532995' && $request->gate_area !='' ) {
            // Log the user in && $request->access_gate != ''

            // print_r($request->gate_area);
            // die();
            Auth::login($user);

            $acc_year = acc_year::where('is_delete',0)->orderBy('id', 'DESC')->first();
            $get_userType = user_type::where('unique_id',$user->user_type)->first();
            // $gateNameGet = AccessGate::where('unique_id',$request->access_gate)->where('is_delete',0)->orderBy('id', 'DESC')->first();
            $areaNameGet = PortAreaAccess::where('unique_id',$request->gate_area)->where('is_delete',0)->orderBy('id', 'DESC')->first();
            
// 'access_gate' => $gateNameGet->access_gate,'access_gateId' => $request->access_gate,
            // Store user information in session
            session(['user_name' => $user->user_name, "email" => $user->email, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id,'acc_year' => $acc_year->acc_year,'UserTypeName' => $get_userType->user_type,'access_area' => $areaNameGet->area_name,'access_areaId' => $request->gate_area]);
            // Session::put(['user_name' => $user->user_name, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id]);

            // Optionally handle logs and other operations here

            return redirect()->route(route: 'Gatedashboard.index')->with('success', 'User Login Successfully');
        } else {
            return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
        }
    }

    public function logout(Request $request)
    {
        // Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('content.authentications.auth-login-basic');
    }
}
