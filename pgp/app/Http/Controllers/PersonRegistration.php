<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\person_registration;
use App\Models\DriverApiDetails;


use App\Models\Person_reg_sublist;
use App\Models\PersonType;
use App\Models\FirmsRegistration;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\passentrysub;



use App\Models\DocumentMaster;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PersonRegistration extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $person_registration = DB::table('person_registration')
                ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
                ->select('person_registration.id', 'person_registration.unique_id',  DB::raw("DATE_FORMAT(person_registration.appDate, '%d-%m-%Y') as formatted_register_date"), 'person_registration.form_unique_id', 'person_registration.app_id_prefix', 'person_registration.app_id', DB::raw("CONCAT(person_registration.app_id_prefix,person_registration.app_id) as merged_app_id"), 'person_registration.appDate', 'person_registration.full_name', 'person_type.person_type', 'person_registration.mobile')
                ->where('person_registration.is_delete', 0)
                ->where('person_type.is_delete', 0)
                // ->where('person_registration.status', 0)

                ->orderBy('person_registration.id', 'desc');

            // dd($request->from_date, $request->to_date);
            if ($request->from_date != '') {



                $person_registration->where('person_registration.register_date', '>=', $request->from_date);
            }

            if ($request->to_date != '') {

                $to_date  = Carbon::parse($request->to_date)->endOfDay();

                $person_registration->where('person_registration.register_date', '<=', $to_date);
            }

            if ($request->has('designation') && $request->designation != '') {
                $person_registration->where('person_registration.designation', 'like', '%' . $request->designation . '%');
            }



            $person_registration = $person_registration->get();

	



            return DataTables::of($person_registration)
                ->addIndexColumn()
                ->addColumn(
                    'action',

                    function ($row) {

                        $edit_url = route('person_registration.edit', $row->form_unique_id);

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

    //

    public function sublist(Request $request)
    {

        // Validate the incoming request data
        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'proof_type'     => 'required|string|max:255',
            'proof_value'    => 'nullable|string|max:255',
            'aadhaar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'licence_proof'  => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'expiry_date'    => 'nullable|max:255',
            'dobdl' => 'nullable',
            'dlholName'    => 'nullable|max:255',
            'dlholBlood'    => 'nullable|max:255',
            'dlrelType'    => 'nullable|max:255',
            'dlDateIssue'    => 'nullable|max:255',
            'dlValidto'    => 'nullable|max:255',
            'dlholAddress'    => 'nullable|max:255',

        ]);

        // Fetch the existing record
        $person_sub = Person_reg_sublist::where('form_unique_id', '=', $request->main_unique_id)
            ->where('proof_type', '=', $request->proof_type)
            ->where('is_delete', '=', 0)
            ->first();

        // Initialize attached_proof
        // $attached_proof = null;

        // Handle the aadhaar proof upload
        if ($request->hasFile('aadhaar_proof')) {
            $aadhaar_proof = $request->file('aadhaar_proof');
            $aadhaar_name  = time() . '-' . $aadhaar_proof->getClientOriginalName();
            $aadhaar_proof->move('uploads/person_registration/aadhaar', $aadhaar_name);

            $attached_proof = $aadhaar_name;
            // New file name for aadhaar proof
        } elseif ($person_sub) {
            // If no new aadhaar proof, retain the existing attached_proof
            $attached_proof = $person_sub->attached_proof;
        }

        // Handle the licence proof upload
        if ($request->hasFile('licence_proof')) {
            $licence_proof = $request->file('licence_proof');
            $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
            $licence_proof->move('uploads/person_registration/license', $licence_name);
            $attached_proof = $licence_name;
            // New file name for licence proof
        } elseif ($person_sub && !$attached_proof) {
            // If no new licence proof, retain the existing attached_proof
            $attached_proof = $person_sub->attached_proof;
        }

        // Prepare the data for update or creation
        $data = [
            'form_unique_id' => $request->main_unique_id,
            'proof_type'     => $request->proof_type,
            'aadhar_licence' => $request->proof_value,
            'expiry_date'    => $request->expiry_date,
            'attached_proof' => $attached_proof, // Use existing or new value

        ];

        // Update the existing record if it exists
        if ($person_sub) {
            // Update the existing record
            $person_sub->update($data);
            return response()->json(['message' => 'Document updated successfully.', 'vehicle' => $person_sub]);
        } else {

            $driverapiData = DriverApiDetails::create([
                'unique_id'      => unique_id(),
                'screen_uniqueId' => $request->main_unique_id,
                'licence_no'     => $request->proof_value,
                'dob' => $request->dobdl,
                'dlholName' => $request->dlholName,
                'dlholBlood' => $request->dlholBlood,
                'dlrelType' => $request->dlrelType,
                'dlDateIssue' => $request->dlDateIssue,
                'dlValidto' => $request->dlValidto,
                'dlholAddress' => $request->dlholAddress,

            ]);
            //dd($driverapiData);

            // Create a new record if none exists
            $person_sublist = Person_reg_sublist::create([
                'unique_id'      => unique_id(),
                'form_unique_id' => $request->main_unique_id,
                'proof_type'     => $request->proof_type,
                'aadhar_licence' => $request->proof_value,
                'expiry_date' => $request->expiry_date,
                'attached_proof' => $attached_proof, // Only the new file name if provided
            ]);

            // dd( $person_sublist );

            return response()->json([
                'message' => 'Sublist created successfully',
                'person_sublist' => $person_sublist,
                'proof_type' => $person_sublist->proof_type,
                'aadhar_licence' => $person_sublist->aadhar_licence,
                'attached_proof' => $person_sublist->attached_proof,
                'unique_id' => $person_sublist->unique_id,
                'expiry_date' => $person_sublist->expiry_date,
            ]);
        }
    }

    public function sublist_update(Request $request, $id)
    {

        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'proof_type'     => 'required|string|max:255',
            'proof_value'    => 'nullable|string|max:255',
            'aadhaar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'licence_proof'  => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);

        // Fetch the existing record
        $person_sub = Person_reg_sublist::where('form_unique_id', '=', $request->main_unique_id)
            ->where('proof_type', '=', $request->proof_type)
            ->where('is_delete', '=', 0)
            ->first();

        if ($request->hasFile('aadhaar_proof')) {
            $aadhaar_proof = $request->file('aadhaar_proof');
            $aadhaar_name  = time() . '-' . $aadhaar_proof->getClientOriginalName();
            $aadhaar_proof->move('uploads/person_registration/aadhaar', $aadhaar_name);
            $attached_proof = $aadhaar_name;
            // New file name for aadhaar proof
        } elseif ($person_sub) {
            // If no new aadhaar proof, retain the existing attached_proof
            $attached_proof = $person_sub->attached_proof;
        }

        // Handle the licence proof upload
        if ($request->hasFile('licence_proof')) {
            $licence_proof = $request->file('licence_proof');
            $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
            $licence_proof->move('uploads/person_registration/licence', $licence_name);
            $attached_proof = $licence_name;
            // New file name for licence proof
        } elseif ($person_sub && !$attached_proof) {
            // If no new licence proof, retain the existing attached_proof
            $attached_proof = $person_sub->attached_proof;
        }

        $person_sublist = Person_reg_sublist::where('id', $id)
            ->where('form_unique_id', '=', $request->main_unique_id)
            ->firstOrFail();
        // dd( $person_sublist );
        $person_sublist->update([
		
            'proof_type' => $request->proof_type,
            'aadhar_licence' => $request->proof_value,
            'expiry_date'   => $request->licence_date,
            'attached_proof'   => $attached_proof
        ]);



        return response()->json([
            'sublist updated successfully',
            'person_sublist' => $person_sublist,
            'proof_type' => $person_sublist->proof_type,
            'aadhar_licence' => $person_sublist->aadhar_licence,
            'attached_proof' => $person_sublist->attached_proof,
            'expiry_date' => $person_sublist->expiry_date
        ]);
    }

    public function edit($form_unique_id)
    {
        // Fetch the main record using form_unique_id
        $person_registration = person_registration::where('form_unique_id', $form_unique_id)
            ->where('is_delete', 0)
            ->first();

        // Check if the main record exists
        if (!$person_registration) {
            return redirect()->back()->with('error', 'Main record not found.');
        }

        // Fetch the sublist data using the form_unique_id from the main record
        $person_sublist_reg = DB::table('person_reg_sublist')->where('person_reg_sublist.is_delete', 0)
            ->where('person_reg_sublist.form_unique_id', $person_registration->form_unique_id)

            ->join('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            ->select('person_reg_sublist.*', 'document_master.type_of_proof as proof_type', 'person_reg_sublist.proof_type as proof_id')
            ->get();

        // Check if the sublist record exists and is not empty
        $person_sublist_reg_first = $person_sublist_reg->first();

        return view('content.person_registration.edit', compact('person_registration', 'person_sublist_reg', 'person_sublist_reg_first'));
    }

    public function main_store(Request $request)
    {

    //  dd($request);
        $request->validate([
            // 'academic_year' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address'       => 'required|string|max:300',
            'designation' => 'required|string|max:255',
            'firm_type'  => 'required|string|max:255',
            'firm_name' =>  'nullable|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'required|string|max:255',
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'main_unique_id'    => 'required',
        ]);

        // Handle the file upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '-' . $photo->getClientOriginalName();
            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }

        // if (person_registration::where('aadhaar_no', $request->aadhaar_no)->exists()) {
        //     return response()->json(['error' => 'AadhaarExists'], 422);
        // }

        // Check for duplicate mobile number
        // if (person_registration::where('mobile', $request->mobile)->exists()) {
        //     return response()->json(['error' => 'MobileExists'], 422);
        // }

        if($request->person_catgorydata != '' || $request->person_catgorydata != null){
            $personCategory = $request->person_catgorydata;
        }else if($request->driver_categorydata != '' || $request->driver_categorydata != null){
            $personCategory = $request->driver_categorydata;
        }else{
	   $personCategory = $request->visitor_categorydata;
	}

        $generate_ids = $this->personRegID($request->designation);
        // dd($generate_ids);
        $password = $request->password;
        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;
        // Create the person registration record
        $person_registration =  person_registration::create([
             'unique_id' => unique_id(),
            'app_id_prefix' => $generate_ids['appIdPrefix'],
            'app_id'        => $generate_ids['appId'],
            'form_unique_id' => $request->main_unique_id,
            'academic_year' =>  acc_year(),
            'full_name' => $request->name,
            'address'       => $request->address,
            // 'register_no' => $request->register_number,
            'designation' => $request->designation,
            'firm_type' => $request->firm_type,
            'firm_name' => $request->firm_name,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,

            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,
            'photo_pic' => $request->photo_pic,
            'image_upload' => isset($photo_name) ? $photo_name : null,
            'declaration' => $request->declaration,
            'person_driver_cat' => $personCategory


            // Save the path of the uploaded photo
        ]);


        return response()->json(['message' => 'Person registered successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            // 'academic_year' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address'       => 'required|string|max:300',
            'designation' => 'required|string|max:255',
            'firm_type'  => 'required|string|max:255',
            'firm_name' =>  'nullable|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'required|string|max:255',
            // 'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'photo' => 'required',
            'main_unique_id'    => 'required',
        ]);

        // Handle the file upload
        // if ($request->hasFile('photo')) {
        //     $photo = $request->file('photo');
        //     $photo_name = time() . '-' . $photo->getClientOriginalName();
        //     $photo->move('uploads/person_registration/person_img/', $photo_name);
        //     // $photo_path = $photo->store( 'uploads', 'public' );
        //     // Store in 'storage/app/public/uploads/photos'
        // }
        $photo_name = $this->storeBase64($request->photo);

        // if (person_registration::where('aadhaar_no', $request->aadhaar_no)->exists()) {
        //     return response()->json(['error' => 'AadhaarExists'], 422);
        // }

        // Check for duplicate mobile number
        // if (person_registration::where('mobile', $request->mobile)->exists()) {
        //     return response()->json(['error' => 'MobileExists'], 422);
        // }

        if($request->person_catgorydata != '' || $request->person_catgorydata != null){
            $personCategory = $request->person_catgorydata;
        }else if($request->driver_categorydata != '' || $request->driver_categorydata != null){
            $personCategory = $request->driver_categorydata;
        }else{
	   $personCategory = $request->visitor_categorydata;
	}

        $generate_ids = $this->personRegID($request->designation);
        // dd($generate_ids);
        $password = $request->password;
        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;
        // Create the person registration record
        $person_registration =  person_registration::create([
             'unique_id' => unique_id(),
            'app_id_prefix' => $generate_ids['appIdPrefix'],
            'app_id'        => $generate_ids['appId'],
            'form_unique_id' => $request->main_unique_id,
            'academic_year' =>  acc_year(),
            'full_name' => $request->name,
            'address'       => $request->address,
            // 'register_no' => $request->register_number,
            'designation' => $request->designation,
            'firm_type' => $request->firm_type,
            'firm_name' => $request->firm_name,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,

            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,
            'photo_pic' => $request->photo_pic,
            'image_upload' => isset($photo_name) ? $photo_name : null,
            'declaration' => $request->declaration,
            'person_driver_cat' => $personCategory


            // Save the path of the uploaded photo
        ]);


        return response()->json(['message' => 'Person registered successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }
    public function store_crop(Request $request)
    {

        // dd($request);
        $request->validate([
            // 'academic_year' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address'       => 'required|string|max:300',
            'designation' => 'required|string|max:255',
            'firm_type'  => 'required|string|max:255',
            'firm_name' =>  'nullable|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'required|string|max:255',
            // 'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'photo' => 'required',
            'main_unique_id'    => 'required',
        ]);

        // Handle the file upload
        // if ($request->hasFile('photo')) {
        //     $photo = $request->file('photo');
        //     $photo_name = time() . '-' . $photo->getClientOriginalName();
        //     $photo->move('uploads/person_registration', $photo_name);
        //     // $photo_path = $photo->store( 'uploads', 'public' );
        //     // Store in 'storage/app/public/uploads/photos'
        // }
        $photo_name = $this->storeBase64($request->photo);

        // if (person_registration::where('aadhaar_no', $request->aadhaar_no)->exists()) {
        //     return response()->json(['error' => 'AadhaarExists'], 422);
        // }

        // Check for duplicate mobile number
        // if (person_registration::where('mobile', $request->mobile)->exists()) {
        //     return response()->json(['error' => 'MobileExists'], 422);
        // }

        if($request->person_catgorydata != '' || $request->person_catgorydata != null){
            $personCategory = $request->person_catgorydata;
        }else if($request->driver_categorydata != '' || $request->driver_categorydata != null){
            $personCategory = $request->driver_categorydata;
        }else{
	   $personCategory = $request->visitor_categorydata;
	}

        $generate_ids = $this->personRegID($request->designation);
        // dd($generate_ids);
        $password = $request->password;
        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;
        // Create the person registration record
        $person_registration =  person_registration::create([
             'unique_id' => unique_id(),
            'app_id_prefix' => $generate_ids['appIdPrefix'],
            'app_id'        => $generate_ids['appId'],
            'form_unique_id' => $request->main_unique_id,
            'academic_year' =>  acc_year(),
            'full_name' => $request->name,
            'address'       => $request->address,
            // 'register_no' => $request->register_number,
            'designation' => $request->designation,
            'firm_type' => $request->firm_type,
            'firm_name' => $request->firm_name,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,

            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,
            'photo_pic' => $request->photo_pic,
            'image_upload' => isset($photo_name) ? $photo_name : null,
            'declaration' => $request->declaration,
            'person_driver_cat' => $personCategory


            // Save the path of the uploaded photo
        ]);


        return response()->json(['message' => 'Person registered successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }
    private function storeBase64($imageBase64)

    {

        list($type, $imageBase64) = explode(';', $imageBase64);

        list(, $imageBase64)      = explode(',', $imageBase64);

        $imageBase64 = base64_decode($imageBase64);

        $imageName= time().'.png';

        $path = public_path() . "/uploads/person_registration/person_img/" . $imageName;

  

        file_put_contents($path, $imageBase64);

          

        return $imageName;

    }
    public function store_main(Request $request)
    {

        // dd($request);
        $request->validate([
            // 'academic_year' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address'       => 'required|string|max:300',
            'designation' => 'required|string|max:255',
            'firm_type'  => 'required|string|max:255',
            'firm_name' =>  'nullable|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'required|string|max:255',
            // 'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'photo' => 'required',
            'main_unique_id'    => 'required',
        ]);

        // Handle the file upload
        // if ($request->hasFile('photo')) {
        //     $photo = $request->file('photo');
        //     $photo_name = time() . '-' . $photo->getClientOriginalName();
        //     $photo->move('uploads/person_registration', $photo_name);
        //     // $photo_path = $photo->store( 'uploads', 'public' );
        //     // Store in 'storage/app/public/uploads/photos'
        // }
        $photo_name = $this->storeBase64($request->photo);

        // if (person_registration::where('aadhaar_no', $request->aadhaar_no)->exists()) {
        //     return response()->json(['error' => 'AadhaarExists'], 422);
        // }

        // Check for duplicate mobile number
        // if (person_registration::where('mobile', $request->mobile)->exists()) {
        //     return response()->json(['error' => 'MobileExists'], 422);
        // }

        $generate_ids = $this->personRegID($request->designation);
        // dd($generate_ids);
        $password = $request->password;
        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;
        // Create the person registration record
        $person_registration =  person_registration::create([
             'unique_id' => unique_id(),
            'app_id_prefix' => $generate_ids['appIdPrefix'],
            'app_id'        => $generate_ids['appId'],
            'form_unique_id' => $request->main_unique_id,
            'academic_year' =>  acc_year(),
            'full_name' => $request->name,
            'address'       => $request->address,
            // 'register_no' => $request->register_number,
            'designation' => $request->designation,
            'firm_type' => $request->firm_type,
            'firm_name' => $request->firm_name,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,

            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,
            'photo_pic' => $request->photo_pic,
            'image_upload' => isset($photo_name) ? $photo_name : null,
            'declaration' => $request->declaration


            // Save the path of the uploaded photo
        ]);


        return response()->json(['message' => 'Person registered successfully', 'person_registration' => $person_registration, 'unique_id' => $person_registration->unique_id, 'aadhaar_no' => $person_registration->aadhaar_no, 'licence_validity_date' => $person_registration->licence_validity_date]);
    }
    // private function storeBase64($imageBase64)

    // {

    //     list($type, $imageBase64) = explode(';', $imageBase64);

    //     list(, $imageBase64)      = explode(',', $imageBase64);

    //     $imageBase64 = base64_decode($imageBase64);

    //     $imageName= time().'.png';

    //     $path = public_path() . "/uploads/person_registration/" . $imageName;

  

    //     file_put_contents($path, $imageBase64);

          

    //     return $imageName;

    // }
        public function personRegID($personType)
        {

            $getpersonType = PersonType::where('unique_id', $personType)->first('person_type');

            $nameParts = explode(' ', $getpersonType->person_type);

        // If there's only one word, just take the first letter
            if (count($nameParts) === 1) {
                $getvalue = strtoupper(substr($nameParts[0], 0, 1)); // Only the first letter of the single word
            }

            $prefix =  $getvalue;
            $new_vaiable = 'REG';
            $year = date('Y');
            $month = date('m');
            $lastFirm = person_registration::where('app_id_prefix', 'like', "$new_vaiable$prefix$year%")
                ->orderBy('app_id', 'desc')
                ->first();

            $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->app_id, -5) : 0;
            $newFirmId = sprintf("%s%s%s%05d", $new_vaiable, $prefix, $year, $lastFirmNumber + 1);

            $app_id_prefix = substr($newFirmId, 0, 8);
            $app_id = substr($newFirmId, -5);
            return ['appIdPrefix' => $app_id_prefix, 'appId' => $app_id];
        }

    public function update(Request $request, $id)
    {
//dd($request);
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'firm_type' => 'required|string|max:255',
            'firm_name' => 'nullable|string|max:255',
            'aadhaar_no' => 'required|string|max:255',
            'licence_validity_date' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'email_id' => 'required|string|max:255',
            'port_type' => 'required|string|max:255',
            'photo_pic' => 'nullable|max:255',
            // 'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            // 'photo' => 'nullable|string|mimes:jpg,jpeg,png|max:2048',
            // 'photo' => 'required',
            'previous_photo' => 'nullable|string|max:2048',
            'declaration' => 'required|in:1',


        ]);

        // Initialize the variable to store the photo name
        
        // Default to the previous photo

        // Handle the file upload if a new photo is provided
        // if ($request->hasFile('photo')) {
        //     $photo = $request->file('photo');
        //     $photo_name = time() . '-' . $photo->getClientOriginalName();
        //     $photo->move('uploads/person_registration/person_img', $photo_name);
        // }
        // dd($request->input('previous_photo'));
        if($request->input('previous_photo') == ''){
        $photo_name = $this->storeBase64up($request->photo);
    }else{
        $photo_name = $request->input('previous_photo');
    }
        // Find the existing person registration record
        $person_registration = person_registration::findOrFail($id);

        if ($request->filled('password') && $request->filled('confirm_password')) {
            $person_authentication_update['password'] = Hash::make($request->password);
            $person_authentication_update['confirm_pass'] = Hash::make($request->confirm_password);
        }


	        if($request->person_catgorydata != '' || $request->person_catgorydata != null){
            $personCategory = $request->person_catgorydata;
		//dd($request->person_catgorydata);

        }else if($request->driver_catgorydata  != '' || $request->driver_catgorydata != null){
            $personCategory = $request->driver_categorydata;
		dd('Hi2',$request->driver_categorydata);
        }else if($request->visitor_categorydata  != '' || $request->visitor_categorydata != null){
            $personCategory = $request->visitor_categorydata;
		//dd('Hi3',$request->visitor_categorydata);
	}


        // Prepare the update data
        $person_registration_update = [
            'full_name' => $request->name,
            'address'       => $request->address,
            'designation' => $request->designation,
            'firm_type' => $request->firm_type,
            'firm_name' => $request->firm_name,
            'aadhaar_no' => $request->aadhaar_no,
            'licence_validity_date' => $request->licence_validity_date,

            'mobile' => $request->mobile,
            'blood_group' => $request->blood_group,
            'email_id' => $request->email_id,
            'port_type' => $request->port_type,
            'photo_pic' => $request->photo_pic,
            'image_upload' => $photo_name,
            'declaration' => $request->declaration,
            'person_driver_cat' => $personCategory

            // Use the updated or previous photo name
        ];

        // Update the person registration record
        $person_registration->update($person_registration_update);

        // dd($person_registration);   

        // Return a JSON response
        return response()->json([
            'message' => 'Person registration updated successfully',
            'person_registration' => $person_registration,
            'unique_id' => $person_registration->unique_id,
            'aadhaar_no' => $person_registration->aadhaar_no,
            'licence_validity_date' => $person_registration->licence_validity_date
        ]);
    }

    private function storeBase64up($imageBase64)

    {

        list($type, $imageBase64) = explode(';', $imageBase64);

        list(, $imageBase64)      = explode(',', $imageBase64);

        $imageBase64 = base64_decode($imageBase64);

        $imageName= time().'.png';

        $path = public_path() . "/uploads/person_registration/person_img/" . $imageName;

  

        file_put_contents($path, $imageBase64);

          

        return $imageName;

    }    public function authentication(Request $request)
    {

        $request->validate([
            'main_unique_id' => 'required|string|max:255',
            'user_id'           => 'required|string|max:255',
            'rf_id' => 'required|string|max:255',
            'password' => 'required|max:255',
            'confirm_password' => 'required|max:255|same:password',

        ]);

        $person_authentication = person_registration::where('unique_id', $request->main_unique_id)->first();

        $password = $request->password;

        $rf_id  = $request->rf_id;
        $confirm_password  = $request->confirm_password;

        $hashedPassword = Hash::make($password);
        $hashed_confirm_Password = $hashedPassword;

        if ($person_authentication) {

            if ($request->password) {

                $person_authentication->user_id = $request->user_id;
                $person_authentication->password = $hashedPassword;
                $person_authentication->rf_id = $rf_id;
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

    // public function sublist_edit( $formUniqueId ) {
    //     $sublistItem = Person_reg_sublist::findOrFail( $formUniqueId );
    //     // Return a view for editing with the current sublist data
    //     return view( 'sublist.edit', compact( 'sublistItem' ) );
    // }

    public function sublistindex(Request $request)
    {
        // dd( 'hi' );
        // Retrieve the sublist data;
        $sublist = DB::table('person_reg_sublist')
            ->where('person_reg_sublist.form_unique_id', $request->main_unique_id)
            ->join('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            // ->join('person_registration', 'person_reg_sublist.form_unique_id', '=', 'person_registration.form_unique_id')
            ->select('person_reg_sublist.proof_type as proof_id', 'person_reg_sublist.*', 'document_master.type_of_proof as proof_type')
            ->where('person_reg_sublist.is_delete', 0)
            ->get();

        // dd($sublist);

        // Return the data as a JSON response
        return response()->json($sublist);
    }

    public function deleteSublistItem(Request $request)
    {

        // dd($request->form_unique_id);
        // Find the sublist item by its unique ID, ensuring it's not already soft-deleted
        $sublistItem = Person_reg_sublist::where('unique_id', $request->form_unique_id)->where('is_delete', 0)->first();
        // dd($sublistItem);

        // Check if the sublist item exists
        if (!$sublistItem) {
            return response()->json(['message' => 'Sublist item not found.'], 404);
        }

        // Perform a soft delete by setting is_delete to 1
        $sublistItem->is_delete = 1;

        // Save changes to the database and check for success
        if ($sublistItem->save()) {
            return response()->json(['message' => 'Sublist item deleted successfully.']);
        } else {
            return response()->json(['message' => 'Failed to delete sublist item.'], 500);
        }
    }



    public function document_get(Request $request)
    {

        $person_sublist = DocumentMaster::where('category_type', $request->category_type)
            ->select('unique_id', 'type_of_proof')
            ->where('is_delete', 0)
            ->get();

        // dd($person_sublist);



        return response()->json(['data' => $person_sublist]);
    }


    public function delete($unique_id)
    {
        // Find the entry by unique_id
        $passEntry = Person_reg_sublist::where('unique_id', $unique_id)->first();

        // Check if the entry exists
        if (!$passEntry) {
            return response()->json(['message' => 'Entry not found'], 404);
        }

        // Delete the entry
         $passEntry->is_delete = 1;
        $passEntry->save();

        return response()->json(['message' => 'Entry deleted successfully']);
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

    public function updateHEPNo(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'rfid' => 'required|string',
        ]);
	
$curDate = Carbon::now()->format('Y-m-d h:i A');

	$validpass = PassEntrySub::where('pass_valid_status', 0)
            ->where('rfid_hep_no', $request->rfid)
	    ->where('to_date', '>=', $curDate)
            ->where('is_delete', 0)
            ->orderBy('id', 'desc')->first(); 
if($validpass != ''){
            $passAlready = 'Already Permit Valid. If You Need? Go To Permit Extension';
            return response()->json(['success' => false, 'message' => 'Already Permit Valid. If You Need? Go To Permit Extension']);

}else{
        // Find the person by ID
        $person = person_registration::find($id);

        if ($person) {
            // Update the rfid field (assuming your table column is 'rfid')
            $person->update([
                'rf_id' => $request->rfid,
            ]);

            return response()->json(['success' => true, 'message' => 'HEP NO updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }
}
    }


  

    public function agentFirms(Request $request)
    {
        // dd($request);
        $getagent = FirmsRegistration::where('firms', $request->agentType)->where('is_delete',0)->where('is_active',1)->get(['unique_id', 'name']);

        return response()->json(['getagentData' => $getagent]);
    }


    public function apiDriverDetail($licenceData, $driverdob)
    {

        // $apiAuthKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJ2b2NwYV91c3IiLCJpYXQiOjE3MzQ1MDY3MDgsImFwcHMiOiJkYXRhcHVzaCJ9.vRLFW8LJmUGnNDtc9er5iGU36daEyglrP9Tuz6MaYkUW2KVUeQY6e-7j9Few5G31Tqij1VmursJkVijnAGMyIA';

$apiAuthKey = DB::table('auth_key')
            ->select('api_key')
            ->where('key_name','Bearer')
            ->first();


        // Get the DL number and Date of Birth from the request
        $url = 'https://www.ulipstaging.dpiit.gov.in/ulip/v1.0.0/SARATHI/01';
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiAuthKey->api_key,
        ])->post($url, [
            'dlnumber' => $licenceData,
            'dob' => $driverdob,
        ]);

        // Check if the API call was successful
        if ($response->successful()) {

            $responseData = $response->json();
// dd($responseData);
            // Extract the XML string from the response
            $xmlString = $responseData['response'][0]['response']['dldetobj'][0];

// dd($xmlString);
            return response()->json($xmlString);
        }

        // If the API call failed, return an error message
        return response()->json([
            'error' => 'Unable to fetch data from SARATHI API.',
            'status' => $response->status(),
        ], $response->status());
    }
}
