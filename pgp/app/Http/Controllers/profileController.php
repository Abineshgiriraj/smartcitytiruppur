<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\user_creation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;


class ProfileController extends Controller
{

  public function index()
  {
      return view('content.dashboard.dashboards-analytics');
  }

  public function login()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function ProfileWindowDetials()
  {
      $unique_id = session('user_id');
      $users = DB::table('user_creation')
          ->join('user_types', 'user_creation.user_type', '=', 'user_types.unique_id')
          ->select(
              'user_creation.unique_id',
              'user_creation.staff_name',
              'user_creation.user_name',
              'user_creation.mobile_number',
              'user_creation.posmachineid',
              'user_creation.user_type as user_type_id',
              'user_types.user_type',
              'user_creation.remarks',
              'user_creation.password',

              'user_creation.email_id',
              'user_creation.password'
          )
          ->where('user_creation.is_delete', 0)  // Ensure the user is not deleted
          ->where('user_creation.is_active', 1)  // Ensure the user is active
          ->where('user_creation.unique_id', $unique_id)  // Filter by unique_id
          ->first();  // Using first() to get a single record based on the unique_id


      // Pass the user data to the view
      return view('content.profilepopup', compact('users'));
  }


//   public function update(Request $request, $unique_id)
// {
//     // Validate the input fields
//     $request->validate([
//         'staff_name' => 'required|string|max:255',
//         'user_name' => 'required|string|max:255',
//         'password' => 'nullable|string|min:8|confirmed',
//         'user_type' => 'required|string|max:255',
//         'pos_mac' => 'required|string|max:255',
//         'designation' => 'required|string|max:255',
//         'mobile_number' => 'required|string|max:15',
//         'photo' => 'nullable|file|mimes:jpg,jpeg,png',
//         'photo_pic' => 'nullable|string|min:8',
//         'email_id' => 'required|email|max:255',
//     ]);

//     // Initialize $photo_name to an empty string in case there's no file uploaded
//     $photo_name = '';

//     if ($request->hasFile('photo')) {
//         $photo = $request->file('photo');
//         $photo_name = time() . '-' . $photo->getClientOriginalName();

//         // Get the destination path using the fixed custom path function
//         $destinationPath = custom_public_path('uploads/person_registration');
//         // Move the file to the destination path
//         $photo->move($destinationPath, $photo_name);
//     }

//     // Find the user by unique_id (instead of the default id)
//     $user = user_creation::where('unique_id', $unique_id)->firstOrFail();

//     // Update the user details
//     $user->staff_name = $request->staff_name;
//     $user->user_name = $request->user_name;

//     // Only update the password if it's provided
//     if ($request->filled('password')) {
//         $user->password = Hash::make($request->password);
//     }

//     if ($photo_name != '') {
//         $user->image_upload = $photo_name;
//         $user->photo_pic = $request->photo_pic;
//     }

//     $user->user_type = $request->user_type;
//     $user->posmachineid = $request->pos_mac;
//     $user->remarks = $request->designation;
//     $user->mobile_number = $request->mobile_number;
//     $user->email_id = $request->email_id;
//  onclick="event.preventDefault(); document.getElementById('logout-form').submit();
//     // Save the updated user record
//     $user->save();

//     // Return success response
//     return response()->json(['success' => 'User updated successfully!']);
// }
public function update(Request $request, $unique_id)
{

  // dd($request);
    // Validate the input fields
    $request->validate([
        'staff_name' => 'required|string|max:255',
        'user_name' => 'required|string|max:255',
        'password' => 'nullable|string|min:8|confirmed',
        'user_type' => 'required|string|max:255',
        'pos_mac' => 'required|string|max:255',
        'designation' => 'required|string|max:255',
        'mobile_number' => 'required|string|max:15',
        'photo' => 'nullable|file|mimes:jpg,jpeg,png',
        'photo_pic' => 'nullable|string|min:8',
        'email_id' => 'required|email|max:255',
    ]);

    // Initialize $photo_name to an empty string in case there's no file uploaded
    $photo_name = '';

    if ($request->hasFile('photo')) {
        $photo = $request->file('photo');
        $photo_name = time() . '-' . $photo->getClientOriginalName();

        // Get the destination path using the fixed custom path function
        // $destinationPath = custom_public_path('profile');
        // Move the file to the destination path

        $photo->move('uploads/profile', $photo_name);

        // dd($photo); 

    }

    // Find the user by unique_id (instead of the default id)
    $user = user_creation::where('unique_id', $unique_id)->firstOrFail();

    // Update the user details
    $user->staff_name = $request->staff_name;
    $user->user_name = $request->user_name;

    // Only update the password if it's provided
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    if ($photo_name != '') {
        $user->image_upload = $photo_name;
        $user->photo_pic = $request->photo_pic;
    }

    $user->user_type = $request->user_type;
    $user->posmachineid = $request->pos_mac;
    $user->remarks = $request->designation;
    $user->mobile_number = $request->mobile_number;
    $user->email_id = $request->email_id;

    // Save the updated user record
    $user->save();

    // If the password is updated, log the user out
    if ($request->filled('password')) {
        auth()->logout(); // Log the user out
        session()->invalidate(); // Invalidate the session
        session()->regenerateToken(); // Regenerate CSRF token
    }

    // Return success response
    return response()->json(['success' => 'User updated successfully!']);
}



}
