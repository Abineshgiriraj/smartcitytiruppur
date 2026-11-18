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
use Illuminate\Support\Facades\Hash;

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
            'access_gate_value' => 'required',
            'user_name' => 'required|string',
            'password' => 'required|string',
            'gate_no'  => 'required|string',
        ]);

        // dd($request->access_gate_value);



        // dd($request->gate_area);

        // Attempt to find the user by username and password
        $user = User::where('user_name', $request->user_name)->first();

        $gate_type = $request->access_gate_value;

        //  dd($user);   
        // && Hash::check($request->password, $user->password)


        if ($user && $user->user_type == '670f52142fb6532995' && Hash::check($request->password, $user->password) && $request->gate_area != '' &&   $gate_type == 'IN') {
            // Log the user in && $request->access_gate != ''

            // print_r($request->gate_area);
            // die();
            Auth::login($user);

            $acc_year = acc_year::where('is_delete', 0)->orderBy('id', 'DESC')->first();
            $get_userType = user_type::where('unique_id', $user->user_type)->first();

            // dd($get_userType);
            // $gateNameGet = AccessGate::where('unique_id',$request->access_gate)->where('is_delete',0)->orderBy('id', 'DESC')->first();
            $areaNameGet = PortAreaAccess::where('unique_id', $request->gate_area)->where('is_delete', 0)->orderBy('id', 'DESC')->first();


            $user_type_id = $user->user_type;



            $permissions = menu_permission($user_type_id);

            $permissions_main  = menu_permission_main($user_type_id);

                //  dd($permissions_main);



$data_array_main = [];
foreach ($permissions_main as $main_permission) {
    // Check if 'main_screen_id' exists and is not empty
    if (!empty($main_permission->main_screen_id)) {
        // Explode comma-separated values into an array and merge it with existing data
        $data_array_main = array_merge($data_array_main, explode(',', $main_permission->main_screen_id));
    }
}

// Initialize $data_array_screens to store screen permissions
$data_array_screens = [];
foreach ($permissions as $screens_data) {
    // Check if 'screen_unique_id' exists and is not empty
    if (!empty($screens_data->screen_unique_id)) {
        // Explode comma-separated values into an array and merge it with existing data
        $data_array_screens = array_merge($data_array_screens, explode(',', $screens_data->screen_unique_id));
    }
}


            //foreach ($permissions_main as $main_permission) {
            //    $data_array = explode(',', $main_permission->main_screen_id);
            // }
            //foreach ($permissions as $screens_data) {
              //  $screens = explode(',', $screens_data->screen_unique_id);
            //}


            // 'access_gate' => $gateNameGet->access_gate,'access_gateId' => $request->access_gate,
            // Store user information in session
            session(['user_name' => $user->user_name, "email" => $user->email, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id, 'acc_year' => $acc_year->acc_year, 'UserTypeName' => $get_userType->user_type, 'access_area' => $areaNameGet->area_name, 'access_areaId' => $request->gate_area, "main_screens" => $data_array_main, "screens" => $data_array_screens, 'access_gate' => 'IN', 'gate_no' => $request->gate_no]);
            // Session::put(['user_name' => $user->user_name, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id]);

            // Optionally handle logs and other operations here

            return redirect()->route(route: 'Gatedashboard.index')->with('success', 'User Login Successfully');
        } else if($user && $user->user_type == '676cec89889c272676' && Hash::check($request->password, $user->password) && $request->gate_area != '' && $gate_type == 'OUT'){
// Log the user in && $request->access_gate != ''

            // print_r($request->gate_area);
            // die();
            Auth::login($user);

            $acc_year = acc_year::where('is_delete', 0)->orderBy('id', 'DESC')->first();
            $get_userType = user_type::where('unique_id', $user->user_type)->first();
            // $gateNameGet = AccessGate::where('unique_id',$request->access_gate)->where('is_delete',0)->orderBy('id', 'DESC')->first();
            $areaNameGet = PortAreaAccess::where('unique_id', $request->gate_area)->where('is_delete', 0)->orderBy('id', 'DESC')->first();


            $user_type_id = $user->user_type;


            // dd($user_type_id);



            $permissions = menu_permission($user_type_id);

            $permissions_main  = menu_permission_main($user_type_id);

            // dd($permissions_main);



$data_array_main = [];
foreach ($permissions_main as $main_permission) {
    // Check if 'main_screen_id' exists and is not empty
    if (!empty($main_permission->main_screen_id)) {
        // Explode comma-separated values into an array and merge it with existing data
        $data_array_main = array_merge($data_array_main, explode(',', $main_permission->main_screen_id));
    }
}

// Initialize $data_array_screens to store screen permissions
$data_array_screens = [];
foreach ($permissions as $screens_data) {
    // Check if 'screen_unique_id' exists and is not empty
    if (!empty($screens_data->screen_unique_id)) {
        // Explode comma-separated values into an array and merge it with existing data
        $data_array_screens = array_merge($data_array_screens, explode(',', $screens_data->screen_unique_id));
    }
}


            //foreach ($permissions_main as $main_permission) {
            //    $data_array = explode(',', $main_permission->main_screen_id);
            // }
            //foreach ($permissions as $screens_data) {
              //  $screens = explode(',', $screens_data->screen_unique_id);
            //}


            // 'access_gate' => $gateNameGet->access_gate,'access_gateId' => $request->access_gate,
            // Store user information in session
            session(['user_name' => $user->user_name, "email" => $user->email, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id, 'acc_year' => $acc_year->acc_year, 'UserTypeName' => $get_userType->user_type, 'access_area' => $areaNameGet->area_name, 'access_areaId' => $request->gate_area, "main_screens" => $data_array_main, "screens" => $data_array_screens, 'access_gate' => 'OUT', 'gate_no' => $request->gate_no]);
            // Session::put(['user_name' => $user->user_name, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id]);

            // Optionally handle logs and other operations here

            return redirect()->route( 'Gatedashboard.index')->with('success', 'User Login Successfully');

}else {
            return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
        }
    }

    public function logout(Request $request)
    {
        // Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth-login-basic');

        // return view('content.authentications.auth-login-basic');
    }
}
