<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\acc_year;
use App\Models\Center;
use App\Models\user_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PassUserLoginController extends Controller
{
    public function index()
    {
        return view('content.authentications.passUserLogin');
    }

    public function store(Request $request)
    {
        // return view('content.dashboard.dashboardPassUser');

        // dd($request->centerName);
        $request->validate([
            'centerName' => 'required',
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to find the user by username and password
        $user = User::where('user_name', $request->user_name)
        // ->where('password', $request->password)
        ->first();

        // && Hash::check($request->password, $user->password)
        if ($user && $user->user_type == '670f5205cc28b44056' && Hash::check($request->password, $user->password) && $request->centerName !='') {
            Auth::login($user);

            $acc_year = acc_year::where('is_delete',0)->orderBy('id', 'DESC')->first();
            $get_userType = user_type::where('unique_id',$user->user_type)->first();
            $center_nameGet = Center::where('unique_id',$request->centerName)->where('is_delete',0)->orderBy('id', 'DESC')->first();

            $user_type_id = $user->user_type;

      $permissions = menu_permission($user_type_id);
      $permissions_main  = menu_permission_main($user_type_id);
      

      foreach ($permissions_main as $main_permission) {
        $data_array = explode(',', $main_permission->main_screen_id);
      }
      foreach ($permissions as $screens_data) {
          $screens = explode(',', $screens_data->screen_unique_id);
      }

            session(['user_name' => $user->user_name, "email" => $user->email, "user_type" => $user->user_type, "staff_name" => $user->staff_name, 'user_id' => $user->unique_id, 'centerId' => $request->centerName,'acc_year' => $acc_year->acc_year,'centerName' => $center_nameGet->center_name,'UserTypeName' => $get_userType->user_type,"main_screens" => $data_array, "screens" => $screens]);
           

            return redirect()->route('Centerdashboard.index')->with( 'success', 'User Login Successfully');
        } else {
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