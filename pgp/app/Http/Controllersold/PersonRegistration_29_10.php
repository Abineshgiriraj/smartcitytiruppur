<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\person_registration;

use App\Models\Person_reg_sublist;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PersonRegistration extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $person_registration = DB::table('person_registration')
                ->join('employee_designation', 'person_registration.designation', '=', 'employee_designation.unique_id')
                ->select('person_registration.id', 'person_registration.unique_id', 'person_registration.full_name', 'employee_designation.designation', 'person_registration.nationality', 'person_registration.mobile')
                ->where('person_registration.is_delete', 0);

            if ($request->has('from_date') && $request->from_date != '') {
                $person_registration->where('person_registration.register_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $person_registration->where('person_registration.register_date', '<=', $request->to_date);
            }
            if ($request->has('designation') && $request->designation != '') {
                $person_registration->where('person_registration.designation', 'like', '%' . $request->designation . '%');
            }

            if ($request->has('status') && $request->status != '') {
                $person_registration->where('person_registration.status', $request->status);
            }

            $person_registration = $person_registration->get();

            return DataTables::of($person_registration)
                ->addIndexColumn()
                ->addColumn(
                    'action',

                    function ($row) {

                        $edit_url = route('person_registration.edit', $row->unique_id);

                        $btn =  '<a href ="' .  $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                        $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                        $btn .= '<i class="fas fa-trash-alt"></i>';
                        $btn .= '</a>';

                        return $btn;
                    }
                )

                ->make(true);
        }
        return view('content.person_registration.index');
    }

    public function add()
    {
        return view('content.person_registration.create');
    }

    public function getdata()
    {
        return view('content.person_registration.data');
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'register_number' => 'required|string|max:255',
            'firm_company'   => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'nationality' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'required|string|max:255',
            'photo' => 'required|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048'
        ]);

        // Handle the file upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '-' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/person_registration'), $photo_name);
            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }
        $exists = person_registration::where('aadhaar_no', $request->aadhaar_no)
            ->where('mobile', $request->mobile)
            ->exists();

        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }

        // Create the person registration record
        $person_registration =  person_registration::create([
            'unique_id' => unique_id(),
            'academic_year' => $request->academic_year,
            'full_name' => $request->name,
            'register_no' => $request->register_number,
            'company_name'  => $request->firm_company,
            'designation' => $request->designation,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,
            'nationality' => $request->nationality,
            'country' => $request->country,
            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,

            'photo_pic' => $request->photo_pic,
            'image_upload' => isset($photo_name) ? $photo_name : null // Save the path of the uploaded photo
        ]);

        return response()->json(['message' => 'Person registered successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }

    public function sublist(Request $request)
    {

        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'proof_type'    =>  'required|string|max:255',
            'proof_value'  => 'nullable|string|max:255',
            'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',

        ]);

        $attached_proof = null;

        if ($request->hasFile('aadhaar_proof')) {
            $aadhaar_proof = $request->file('aadhaar_proof');
            $aadhaar_name  = time() . '-' . $aadhaar_proof->getClientOriginalName();
            $aadhaar_proof->move(public_path('uploads/person_registration/aadhaar'), $aadhaar_name);
            $attached_proof =  $aadhaar_name;
            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }

        if ($request->hasFile('licence_proof')) {
            $licence_proof = $request->file('licence_proof');
            $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
            $licence_proof->move(public_path('uploads/person_registration/licence'), $licence_name);
            $attached_proof =  $licence_name;

            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }

        $person_sub = Person_reg_sublist::where('form_unique_id', '=', $request->main_unique_id)
            ->where('proof_type', '=', $request->proof_type)
            ->where('is_delete', '=', 0)
            ->first();

        $data = [

            'form_unique_id'     => $request->main_unique_id,
            'proof_type'         => $request->proof_type,
            'aadhar_licence'     => $request->proof_value,
            'attached_proof'     =>  $attached_proof,

        ];

        // if ( $request->aadhaar_num ) {
        if ($person_sub) {
            // If record exists, update it
            $person_sub->update($data);
            return response()->json(['message' => 'document updated successfully.', 'vehicle' => $person_sub]);
        } else {

            $person_sublist          = Person_reg_sublist::create([
                'unique_id'          => unique_id(),
                'form_unique_id'     => $request->main_unique_id,
                'proof_type'         => $request->proof_type,
                'aadhar_licence'     => $request->proof_value,
                'attached_proof'     =>  $attached_proof,
            ]);

            return response()->json(['sublist created', 'person_sublist' => $person_sublist, 'proof_type' => $person_sublist->proof_type, 'aadhar_licence' => $person_sublist->aadhar_licence, 'attached_proof' => $person_sublist->attached_proof]);
        }
    }

    // public function edit( $unique_id )
    // {

    //     $person_registration =  person_registration::where( 'unique_id', $unique_id )->firstOrFail();
    //     $person_sublist_reg = Person_reg_sublist::where( 'form_unique_id', $unique_id )->firstOrFail();
    //     // dd( $person_sublist_reg );

    //     return view( 'content.person_registration.edit', compact( 'person_registration', 'person_sublist_reg' ) );
    // }

    // public function edit( $unique_id )
    // {
    //     // Find the main record using unique_id
    //     $person_registration = person_registration::where( 'unique_id', $unique_id )->first();

    //     // Check if the main record exists
    //     if ( !$person_registration ) {
    //         // Handle the case where the record does not exist
    //         return redirect()->back()->with( 'error', 'Record not found.' );
    //     }

    //     // Fetch the sublist data using the form_unique_id of the found record
    //     $person_sublist_reg = Person_reg_sublist::where( 'form_unique_id', $person_registration->unique_id )->get();

    //     // Pass the data to the view
    //     return view( 'content.person_registration.edit', compact( 'person_registration', 'person_sublist_reg' ) );
    // }

    // public function edit( $unique_id )
    // {
    //     // Fetch the main record using unique_id
    //     $person_registration = person_registration::where( 'unique_id', $unique_id )->first();

    //     // Check if the main record exists
    //     if ( !$person_registration ) {
    //         return redirect()->back()->with( 'error', 'Main record not found.' );
    //     }

    //     // Fetch the sublist data using the form_unique_id from the main record
    //     $person_sublist_reg = Person_reg_sublist::where( 'form_unique_id', $person_registration->unique_id )->get();

    //     // dd( $person_sublist_reg );

    //     // Check if the sublist record exists and is not empty
    //     if ( $person_sublist_reg->isEmpty() ) {
    //         $person_sublist_reg = [];
    // Set to empty array to avoid errors in the view
    //     }

    //     return view( 'content.person_registration.edit', compact( 'person_registration', 'person_sublist_reg' ) );
    // }

    // public function edit( $unique_id )
    // {
    //     // Fetch the main record using unique_id
    //     $person_registration = person_registration::where( 'unique_id', $unique_id )->first();

    //     // Check if the main record exists
    //     if ( !$person_registration ) {
    //         return redirect()->back()->with( 'error', 'Main record not found.' );
    //     }

    //     // Fetch the sublist data using the form_unique_id from the main record
    //     $person_sublist_reg = Person_reg_sublist::where( 'form_unique_id', $person_registration->unique_id )
    //         ->select( 'id', 'form_unique_id', 'proof_type', 'aadhar_licence', 'attached_proof' )
    //         ->get();
    //     // dd( $person_sublist_reg );
    //     // Check if the sublist record exists and is not empty
    //     if ( $person_sublist_reg->isEmpty() ) {
    //         $person_sublist_reg = [];
    // Set to empty array to avoid errors in the view
    //     }

    //     return view( 'content.person_registration.edit', compact( 'person_registration', 'person_sublist_reg' ) );
    // }

    public function edit($unique_id)
    {
        // Fetch the main record using unique_id
        $person_registration = person_registration::where('unique_id', $unique_id)
            ->where('is_delete', 0)
            ->first();

        // Check if the main record exists
        if (!$person_registration) {
            return redirect()->back()->with('error', 'Main record not found.');
        }

        // Fetch the sublist data using the form_unique_id from the main record
        $person_sublist_reg = Person_reg_sublist::where('form_unique_id', $person_registration->unique_id)
            ->where('is_delete', 0)
            ->get();

        // Check if the sublist record exists and is not empty
        $person_sublist_reg_first = $person_sublist_reg->first();

        return view('content.person_registration.edit', compact('person_registration', 'person_sublist_reg', 'person_sublist_reg_first'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'firm_company' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'nationality' => 'required|string|max:255',
            'country' => 'required|string|max:255',

            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',

            'photo_pic' => 'nullable|string|max:255',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'previous_photo' => 'nullable|string|max:2048'

        ]);


        // $photo->move(public_path('uploads/person_registration'), $photo_name);

        // Handle the file upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '-' . $photo->getClientOriginalName();
         $photo->move(public_path('uploads/person_registration'), $photo_name);

            // $photo->move(public_path('uploads/person_registration'), $photo_name);
            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }

        else {
            $photo_name = $request->input('previous_photo'); // Use the old image path
        }

        $person_registration =  person_registration::findOrFail($id);

        $person_registration_update = [
            'full_name' =>  $request->name,
            'company_name' =>  $request->firm_company,
            'designation' =>  $request->designation,
            'aadhaar_no' =>  $request->aadhaar_no,
            'licence_validity_date' =>  $request->licence_validity_date,
            'nationality' =>  $request->nationality,
            'country' =>  $request->country,
            'mobile' =>  $request->mobile,
            'blood_group' =>  $request->blood_group,
            'email_id' =>  $request->email_id,
            'port_type' =>  $request->port_type,
            'photo_pic' =>  $request->photo_pic,
            'image_upload' =>  $request->photo_name,
        ];

        $person_registration->update($person_registration_update);

        // $person_registration->update( [
        //     $person_registration->full_name =  $request->name,
        //     $person_registration->designation =  $request->designation,
        //     $person_registration->aadhaar_no =  $request->aadhaar_no,
        //     $person_registration->licence_validity_date =  $request->licence_validity_date,
        //     $person_registration->nationality =  $request->nationality,
        //     $person_registration->country =  $request->country,
        //     $person_registration->mobile =  $request->mobile,
        //     $person_registration->blood_group =  $request->blood_group,
        //     $person_registration->email_id =  $request->email_id,
        //     $person_registration->photo_pic =  $request->photo_pic,
        //     $person_registration->image_upload =  $request->photo,

        // ] );
        // $person_registration->update( $request );
        // dd( $person_registration );

        // $person_registration =  person_registration::create( [
        //     'unique_id' => unique_id(),
        //     'full_name' => $request->name,
        //     'designation' => $request->designation,
        //     'aadhaar_no' => $request->aadhaar_no,
        //     'licence_validity_date' => $request->licence_validity_date,
        //     'nationality' => $request->nationality,
        //     'country' => $request->country,
        //     'mobile' => $request->mobile,
        //     'blood_group' => $request->blood_group,
        //     'email_id' => $request->email_id,
        //     'photo_pic' => $request->photo_pic,
        //     'image_upload' => isset( $photo_name ) ? $photo_name : null // Save the path of the uploaded photo
        // ] );

        return response()->json(['message' => 'Person registration Updated successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }

    public function authentication(Request $request)
    {

        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'user_id'           => 'required|string|max:255',
            // 'authentication_type' => 'required|string|max:255',
            'rf_id' => 'required|string|max:255',
            'password' => 'required|max:255',
            'confirm_password' => 'required|max:255|same:password',

        ]);

        $person_authentication = person_registration::where('unique_id', $request->main_unique_id)->first();

        $password = $request->password;

        $confirm_password  = $request->confirm_password;

        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;

        if ($person_authentication) {

            if ($request->password) {




                $person_authentication->user_id = $request->user_id;
                $person_authentication->password = $hashedPassword;
                $person_authentication->confirm_password = $hashedPassword;
            }


            $person_authentication->save();

            return response()->json(['Authentication created Successfully'], 200);
        } else {
            // If the record doesn't exist, return an error message
            return response()->json(['error' => 'Record not found for the provided main_unique_id.'], 404);
        }

        // if($request->password){
        //     $person_authentication =  person_registration::create([
        //         'password'    => $request->password,
        //         'confirm_password' =>  $request->confirm_password,
        //         'pin_num'           => $request->pin_num,
        //         'confirm_pin'           => $request->confirm_pin,


        //     ]);
        // }
    }


    public function authentication_update(Request $request)
    {

        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'user_id'           => 'required|string|max:255',
            // 'authentication_type' => 'required|string|max:255',
            'rf_id' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'confirm_password' => 'nullable|string|max:255|same:password',

        ]);

        $person_authentication = person_registration::where('unique_id', $request->main_unique_id)->first();

        $person_authentication_update = [

            'unique_id' => $request->main_unique_id,
            'rf_id' => $request->rf_id,
            'user_id'  =>  $request->user_id,


        ];

        // $password = $request->password;

        // $confirm_password  = $request->confirm_password;

        // $hashedPassword = Hash::make($password);
        // $hashed_confirm_Password = Hash::make($confirm_password);

        if ($request->filled('password') && $request->filled('confirm_password')) {
            $person_authentication_update['password'] = Hash::make($request->password);
            $person_authentication_update['confirm_pass'] = Hash::make($request->confirm_password);
        }

        $person_authentication->update($person_authentication_update);

        return response()->json(['Authentication updated Successfully'], 200);



        // if ($person_authentication) {

        //     if ($request->password) {
        //         // $person_authentication->authentication_type = $request->authentication_type;
        //         // cation->confirm_password = bcrypt( $request->confirm_password );
        //         $person_authentication->unique_id = $request->main_unique_id;
        //         $person_authentication = $request->user_id;

        //         $person_authentication->password = $hashedPassword;
        //         $person_authentication->confirm_password = $hashed_confirm_Password;
        //     }
        //     // $person_authenti

        //     $person_authentication->save();

        //
        // } else {
        //     // If the record doesn't exist, return an error message
        //     return response()->json( [ 'error' => 'Record not found for the provided main_unique_id.' ], 404 );
        // }

        // if ( $request->password ) {
        //     $person_authentication =  person_registration::create( [
        //         'password'    => $request->password,
        //         'confirm_password' =>  $request->confirm_password,
        //         'pin_num'           => $request->pin_num,
        //         'confirm_pin'           => $request->confirm_pin,

        // ] );
        // }
    }

    public function destroy($id)
    {

        $person_registration  = person_registration::findOrFail($id);

        $person_registration->is_delete = 1;
        $person_registration->save();

        return redirect()->route('person_registration')->with('success', 'Person Data deleted successfully!');
    }

    public function getPersonSublistData(Request $request)
    {

        $main_unique_id = $request->input('main_unique_id');

        $sub_list_data = Person_reg_sublist::where('main_unique_id', $main_unique_id)->get();

        return response()->json([
            'data' => $sub_list_data
        ]);
    }

    public function sublist_edit($formUniqueId)
    {
        $sublistItem = Person_reg_sublist::findOrFail($formUniqueId);
        // Return a view for editing with the current sublist data
        return view('sublist.edit', compact('sublistItem'));
    }

    public function deleteSublistItem($form_unique_id)
    {
        // Find the sublist item by its unique ID
        $sublistItem = Person_reg_sublist::where('form_unique_id', $form_unique_id)->first();

        // Check if the sublist item exists
        if (!$sublistItem) {
            return response()->json(['message' => 'Sublist item not found.'], 404);
        }

        // Set the is_delete field to 1 ( soft delete )
        $sublistItem->is_delete = 1;

        // Save changes to the database and check for success
        if ($sublistItem->save()) {
            return response()->json(['message' => 'Sublist item deleted successfully.']);
        } else {
            return response()->json(['message' => 'Failed to delete sublist item.'], 500);
        }
    }

    public function getLastregpersonNumber()
    {
        $year = date('Y');
        // Current year
        $month = date('m');
        // Current month, formatted as two digits

        // Fetch the last firm number for the current year and month
        $lastFirm = person_registration::where('register_no', 'like', "PER-$year$month%")
            ->orderBy('register_no', 'desc')
            ->first();

        // dd( $lastFirm );

        // Extract the last number, default to 0 if no previous records exist
        $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->register_no, -4) : 0;

        // Increment the number for the new firm ID
        $newFirmId = sprintf('PER-%s%s%04d', $year, $month, $lastFirmNumber + 1);

        return response()->json(['register_no' => $newFirmId]);
    }
}
