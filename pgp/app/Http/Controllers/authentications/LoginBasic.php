<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmployeeRegistration;
use Illuminate\Support\Facades\Hash;
use App\Models\user_type;
use App\Models\staffRegistration;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function showloginform()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function store(Request $request)
  {
    $request->validate([
      'user_name' => 'required|string',
      'password' => 'required|string',
    ]);

    $user = User::where('user_name', $request->user_name)->first();

    if ($user && Hash::check($request->password, $user->password)) {
      Auth::login($user);

      $get_userType = user_type::where('unique_id', $user->user_type)->first();
      if (!$get_userType) {
        $get_userType = staffRegistration::where('unique_id', $user->user_type)->first();
      }

      $user_type_id = $user->user_type;

      $permissions = menu_permission($user_type_id);
      $permissions_main = menu_permission_main($user_type_id);

      $data_array_main = [];
      foreach ($permissions_main as $main_permission) {
        if (!empty($main_permission->main_screen_id)) {
          $data_array_main = array_merge($data_array_main, explode(',', $main_permission->main_screen_id));
        }
      }

      $data_array_screens = [];
      foreach ($permissions as $screens_data) {
        if (!empty($screens_data->screen_unique_id)) {
          $data_array_screens = array_merge($data_array_screens, explode(',', $screens_data->screen_unique_id));
        }
      }

      session([
        'user_name' => $user->user_name,
        "email" => $user->email,
        "user_type" => $user->user_type,
        "staff_name" => $user->staff_name,
        'user_id' => $user->unique_id,
        'UserTypeName' => $get_userType->user_type ?? '',
        'profile' => $user->image_upload,
        "main_screens" => $data_array_main,
        "screens" => $data_array_screens
      ]);

      return redirect()->route('dashboard.index')->with('success', 'User Login Successfully');
    } elseif (!$user) {
      $employee = staffRegistration::where('user_name', $request->user_name)->first();

      // Plain text password check for staffRegistration
      if ($employee && $request->password === $employee->new_password) {

        $get_userType = user_type::where('unique_id', $employee->user_type)->first();
        if (!$get_userType) {
          $get_userType = staffRegistration::where('unique_id', $employee->user_type)->first();
        }

        $user_type_id = $employee->user_type;

        $permissions = menu_permission($user_type_id);
        $permissions_main = menu_permission_main($user_type_id);

        $data_array_main = [];
        foreach ($permissions_main as $main_permission) {
          if (!empty($main_permission->main_screen_id)) {
            $data_array_main = array_merge($data_array_main, explode(',', $main_permission->main_screen_id));
          }
        }

        $data_array_screens = [];
        foreach ($permissions as $screens_data) {
          if (!empty($screens_data->screen_unique_id)) {
            $data_array_screens = array_merge($data_array_screens, explode(',', $screens_data->screen_unique_id));
          }
        }

        session([
          'user_name' => $employee->user_name,
          "email" => $employee->email_id,
          "user_type" => $employee->user_type,
          "staff_name" => $employee->fullname,
          'user_id' => $employee->unique_id,
          'UserTypeName' => $get_userType->user_type ?? '',
          "main_screens" => $data_array_main,
          "screens" => $data_array_screens
        ]);

        return redirect()->route('dashboard.index')->with('success', 'User Login Successfully');
      } else {
        return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
      }
    } else {
      return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
    }
  }

  public function logout(Request $request)
  {
    // Auth::logout();
    // $request->session()->invalidate();
    // $request->session()->regenerateToken();

    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('authlogin');

    // return view('content.authentications.auth-login-basic');
  }
}
