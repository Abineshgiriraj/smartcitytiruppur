<?php

namespace App\Http\Controllers;

use App\Models\FirmsRegistration;
use Illuminate\Http\Request;
use App\Models\FirmsRegistrationSub;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;


class FirmsRegistrationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $firms_registration = DB::table('firms_registration')
                ->join('company_creation', 'firms_registration.firms', '=', 'company_creation.unique_id')
                ->select(
                    'firms_registration.id',
                    'firms_registration.unique_id',
                    'entry_date',
                    'firms_registration.firm_id',
                    'name',
                    'company_creation.company_name',
                    'firms_registration.mobile'
                )
                ->where('firms_registration.is_delete', 0);



            if ($request->has('from_date') && $request->from_date != '') {
                $firms_registration->where('firms_registration.entry_date', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && $request->to_date != '') {
                $firms_registration->where('firms_registration.entry_date', '<=', $request->to_date);
            }

            if ($request->has('firms') && $request->firms != '') {
                $firms_registration->where('firms_registration.firms', 'like', '%' . $request->firms . '%');
            }
            if ($request->has('name') && $request->name != '') {
                $firms_registration->where('firms_registration.name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('status')) {
                $firms_registration->where('firms_registration.status', $request->status);
            }
            $firms_registration->get();

            // dd($firms_registration);

            return DataTables::of($firms_registration)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit_url = route('firms_registration.edit', $row->id);

                    $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</a>';
                    return $btn;
                })
                ->make(true);
        }

        // For the non-AJAX request, return the main view
        return view('content.firms_registration.index');
    }


    public function add()
    {
        return view('content.firms_registration.form');
    }
    public function edit($id)
    {
        // Fetch the main record using unique_id
        $firms_registration = FirmsRegistration::where('id', $id)
            ->where('is_delete', 0)
            ->first();
        // Check if the main record exists
        if (!$firms_registration) {
            return redirect()->back()->with('error', 'Main record not found.');
        }
        // Fetch the sublist data using the form_unique_id from the main record
        $firms_sublist_reg = FirmsRegistrationSub::where('form_unique_id', $firms_registration->unique_id)
            ->where('is_delete', 0)
            ->get();
        // dd($firms_sublist_reg);

        $existing_firm_id = $firms_registration->firm_id;

        return view('content.firms_registration.edit', compact('firms_registration', 'firms_sublist_reg', 'existing_firm_id'));
    }



    public function store(Request $request)
    {
        $currentDate = now()->format('Y-m-d');

        $request->validate([
            'academic_year' => 'required',
            'firms' => 'required',
            'firm_id' => 'required',
            'company_reg_no' => 'nullable|string',
            'name' => 'required|string|max:255',
            'aadhar_no' => 'required',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email',
            'gstin' => 'nullable|string|max:15',
            'address' => 'nullable|string',
    //         'adhaar_upload' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    // 'signature_upload_1' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    // 'signature_upload_2' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
     'pan_no' => 'nullable|string|max:15',
            'proprietor_name' => 'required|string',
            'adhaar_1' => 'required|string',
            'proprietor_name_1' => 'required|string',
            'email_1' => 'required|string',
            'otp' => 'required|string',
            'mobile_1' => 'required|string',
            'proprietor_name_2' => 'required|string',
            'adhaar_2' => 'required|string',
            'email_2' => 'required|string',
            'mobile_2' => 'required|string',
            // 'adhaar_upload_2' =>  'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'proprietor_name_2' => 'required|string',
            'screen_unique_id' => 'required',
            'login_id' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Store the firm_id from the request
        $firms_registration = FirmsRegistration::create([
            'unique_id' => unique_id(),
            'form_unique_id' => $request->screen_unique_id,
            'academic_year' => $request->academic_year,
            'firms' => $request->firms,
            'firm_id' => $request->firm_id, // Use the firm_id from the request
            'company_reg_no' => $request->company_reg_no,
            'name' => $request->name,
            'aadhar_no' => $request->aadhar_no,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'pan_no' => $request->pan_no,
            'proprietor_name' => $request->proprietor_name,
            'gstin' => $request->gstin,
            'address' => $request->address,
            'adhaar_1' => $request->adhaar_1,
'adhaar_2' => $request->adhaar_2,
'mobile_2' => $request->mobile_2,
            'proprietor_name_1' => $request->proprietor_name_1,
            'email_1' => $request->email_1,
            'mobile_1' => $request->mobile_1,
            'otp' => $request->otp,
            'proprietor_name_2' => $request->proprietor_name_2,
            'email_2' => $request->email_2,
            // 'adhaar_upload_2' => $request->adhaar_upload_2,
            'proprietor_name_2' => $request->proprietor_name_2,
            // 'adhaar_upload' => $request->adhaar_upload,
            // 'signature_upload_1' => $request->signature_upload_1,
            // 'signature_upload_2' => $request->signature_upload_2,
            'entry_date' => $currentDate,
            'login_id' => $request->login_id,
            'password' => $request->password,
            'port_status' => 1,

        ]);
        // dd($firms_registration);
       

        return response()->json(['status' => 'Firms registered successfully']);
        //     return response()->json(['message' => 'Firms registered successfully', 'firms_registration' => $firms_registration, 'unique_id' => $firms_registration->unique_id, 'login_id' => $firms_registration->aadhar_no]);
            // return response()->json(['error' => 'Failed to register firm.'], 500);
        
    }
        // dd('entry_date');
        //
    // }

    public function getLastFirmNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Fetch the last firm registration matching the pattern YYYYMM%
        $lastFirm = FirmsRegistration::where('firm_id', 'like', "$year$month%")
            ->orderBy('firm_id', 'desc')
            ->first();

        // Extract the last 5 digits and increment them
        $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->firm_id, -4) : 0;

        // Generate a new firm_id in the format YYYYMMxxxxx
        $newFirmId = sprintf("%s%s%05d", $year, $month, $lastFirmNumber + 1);

        // Return the new firm_id as a JSON response
        return response()->json(['firm_id' => $newFirmId]);
    }




    public function sublist(Request $request)
    {
        $request->validate([
            'screen_unique_id' => 'required|string|max:255',
            'proof_type' => 'required',
            'proof_value' => 'required',
            'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            // 'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            // 'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);
      
        $attached_proof = null;

        $person_sub = FirmsRegistrationSub::where('form_unique_id', '=', $request->screen_unique_id)
            ->where('proof_type', '=', $request->proof_type)
            ->where('is_delete', '=', 0)
            ->first();


        $attached_proof = $person_sub ? $person_sub->attached_proof : null;



        if ($request->hasFile('licence_proof')) {
            $licence_proof = $request->file('licence_proof');
            $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
            $licence_proof->move(public_path('uploads/firms_registration/licence'), $licence_name);
            $attached_proof =  $licence_name;

            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }
        
       
        $data = [

            'form_unique_id'     => $request->screen_unique_id,
            'proof_type'         => $request->proof_type,
            'aadhar_licence'     => $request->proof_value,
            'attached_proof'     =>  $attached_proof,

        ];
        
        if ($person_sub) {
            // If record exists, update it
            $person_sub->update($data);
            return response()->json(['message' => 'document updated successfully.', 'vehicle' => $person_sub]);
        } else {
            // Create a new entry in the database
            $FirmsRegistrationSub = FirmsRegistrationSub::create([

                'form_unique_id' => $request->screen_unique_id,
                'proof_type' => $request->proof_type,
                'aadhar_licence' => $request->proof_value, // Corrected to use the right key
                'attached_proof' => $attached_proof,
                'unique_id' => unique_id()
            ]);


            return response()->json([
                'message' => 'sublist created',

                'FirmsRegistrationSub' => $FirmsRegistrationSub,
                // 's_no' =>$FirmsRegistrationSub->s_no,
                'proof_type' => $FirmsRegistrationSub->proof_type,
                'aadhar_licence' => $FirmsRegistrationSub->aadhar_licence,
                'attached_proof' => $FirmsRegistrationSub->attached_proof
            ]);
        }
        
    }
    public function authentication(Request $request)
    {
        $request->validate([
            'screen_unique_id' => 'string|max:255',
            'login_id' => 'required|string|max:255',  // Validate login_id as well
            'password' => 'required|string|max:255',
            'confirm_password' => 'required|string|max:255|same:password',
        ]);

        $firms_authentication = FirmsRegistration::where('unique_id', $request->screen_unique_id)->first();

        if ($firms_authentication) {
            // Hash the password
            $hashedPassword = Hash::make($request->password);

            // Store the hashed password and login ID
            $firms_authentication->password = $hashedPassword;
            $firms_authentication->login_id = $request->login_id;
            $firms_authentication->confirm_password = $hashedPassword;
            $firms_authentication->save();

            return response()->json(['message' => 'Authentication created successfully'], 200);
        } else {
            return response()->json(['error' => 'Record not found for the provided main_unique_id.'], 404);
        }
    }
    public function authentication_update(Request $request)
    {
        $request->validate([
            'screen_unique_id' => 'string|max:255',
            'login_id' => 'required|string|max:255',  // Validate login_id as well
            'password' => 'nullable|string|max:255',
            'confirm_password' => 'nullable|string|max:255|same:password',
        ]);

        $firms_authentication = FirmsRegistration::where('unique_id', $request->screen_unique_id)->first();

        if ($firms_authentication) {
            // Hash the password
            $hashedPassword = Hash::make($request->password);

            // Store the hashed password and login ID
            $firms_authentication->unique_id = $request->screen_unique_id;
            $firms_authentication->password = $hashedPassword;
            $firms_authentication->login_id = $request->login_id;
            $firms_authentication->confirm_password = $hashedPassword;
            $firms_authentication->save();

            return response()->json(['message' => 'Authentication created successfully'], 200);
        } else {
            return response()->json(['error' => 'Record not found for the provided main_unique_id.'], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'firms' => 'required',
            'academic_year' => 'required',
            'firm_id' => 'required',
            'company_reg_no' => 'nullable|string',
            'name' => 'required|string|max:255',
            'aadhar_no' => 'required',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email', // Added email validation
            'contact_person' => 'nullable|string|max:255',
            'fax_no' => 'nullable|string|max:15',
            'gstin' => 'nullable|string|max:15',
            'address_1' => 'nullable|string',
            'address_2' => 'nullable|string',
        ]);

        $firm = FirmsRegistration::findOrFail($id); // Find the firm by ID or fail

        // Update firm details
        $firm->update([
            'firms' => $request->firms,
            'academic_year' => $request->academic_year,
            'firm_id' => $request->firm_id,
            'company_reg_no' => $request->company_reg_no,
            'name' => $request->name,
            'aadhar_no' => $request->aadhar_no,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'fax_no' => $request->fax_no,
            'gstin' => $request->gstin,
            'address_1' => $request->address_1,
            'address_2' => $request->address_2,
            
        ]);

        return response()->json(['message' => 'Firm registration updated successfully', 'firm' => $firm]);
    }


    // public function sublist(Request $request)
    // {
    //     $request->validate([
    //         'main_unique_id' => 'required|string|max:255',
    //         'proof_type' => 'required',
    //         'proof_value' => 'required',
    //         'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    //         'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    //         // 'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    //         // 'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
    //     ]);

    //     $attached_proof = null;
    //     // Check if the file is present and handle the upload
    //     // if ($request->hasFile('aadhaar_license')) {
    //     //     $aadhaar_proof = $request->file('aadhaar_license'); // Correct key for file input
    //     //     $aadhaar_license = time() . '-' . $aadhaar_proof->getClientOriginalName();
    //     //     $aadhaar_proof->move(public_path('uploads/firms_registration/aadhaar'), $aadhaar_license);
    //     //     $attached_proof = $aadhaar_license;

    //     // }

    //     if ($request->hasFile('aadhaar_proof')) {
    //         $aadhaar_proof = $request->file('aadhaar_proof');
    //         $aadhaar_name  = time() . '-' . $aadhaar_proof->getClientOriginalName();
    //         $aadhaar_proof->move(public_path('uploads/firms_registration/aadhaar'), $aadhaar_name);
    //         $attached_proof =  $aadhaar_name;
    //         // $photo_path = $photo->store( 'uploads', 'public' );
    //         // Store in 'storage/app/public/uploads/photos'
    //     }

    //     if ($request->hasFile('licence_proof')) {
    //         $licence_proof = $request->file('licence_proof');
    //         $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
    //         $licence_proof->move(public_path('uploads/firms_registration/licence'), $licence_name);
    //         $attached_proof =  $licence_name;

    //         // $photo_path = $photo->store( 'uploads', 'public' );
    //         // Store in 'storage/app/public/uploads/photos'
    //     }
    //     // dd($aadhaar_proof);die();
    //     $person_sub = FirmsRegistrationSub::where('form_unique_id', '=', $request->main_unique_id)
    //         ->where('proof_type', '=', $request->proof_type)
    //         ->where('is_delete', '=', 0)
    //         ->first();

    //     $data = [

    //         'form_unique_id'     => $request->main_unique_id,
    //         'proof_type'         => $request->proof_type,
    //         'aadhar_licence'     => $request->proof_value,
    //         'attached_proof'     =>  $attached_proof,

    //     ];
    //     if ($person_sub) {
    //         // If record exists, update it
    //         $person_sub->update($data);
    //         return response()->json(['message' => 'document updated successfully.', 'vehicle' => $person_sub]);
    //     } else {
    //         // Create a new entry in the database
    //         $FirmsRegistrationSub = FirmsRegistrationSub::create([

    //             'form_unique_id' => $request->main_unique_id,
    //             'proof_type' => $request->proof_type,
    //             'aadhar_licence' => $request->proof_value, // Corrected to use the right key
    //             'attached_proof' => $attached_proof,
    //             'unique_id' => unique_id()
    //         ]);


    //         return response()->json([
    //             'message' => 'sublist created',

    //         ]);
    //     }
    // }

    public function deletesub(Request $request, $id)
    {
        $request->validate([
            'is_delete' => 'required|boolean',
        ]);

        $document = FirmsRegistrationSub::find($id);

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }
        $document->is_delete = $request->is_delete;
        $document->save();

        return response()->json(['success' => true]);
    }



    public function sublistindex(Request $request)
    {
        // Retrieve the sublist data;
        $sublist = FirmsRegistrationSub::where('is_delete', 0)->where('form_unique_id', $request->screen_unique_id)->get();

        // Return the data as a JSON response
        return response()->json($sublist);
    }


    public function sublist_update(Request $request, $id)
    {


        $request->validate([
            'screen_unique_id' => 'required|string|max:255',
            'proof_type' => 'required',
            'proof_value' => 'required',
            'aadhaar_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'licence_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);


        if ($request->hasFile('aadhaar_proof')) {
            $aadhaar_proof = $request->file('aadhaar_proof');
            $aadhaar_name  = time() . '-' . $aadhaar_proof->getClientOriginalName();
            $aadhaar_proof->move(public_path('uploads/firms_registration/aadhaar'), $aadhaar_name);
            $attached_proof =  $aadhaar_name;
            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }

        if ($request->hasFile('licence_proof')) {
            $licence_proof = $request->file('licence_proof');
            $licence_name  = time() . '-' . $licence_proof->getClientOriginalName();
            $licence_proof->move(public_path('uploads/firms_registration/licence'), $licence_name);
            $attached_proof =  $licence_name;

            // $photo_path = $photo->store( 'uploads', 'public' );
            // Store in 'storage/app/public/uploads/photos'
        }
        $firm_sublist = FirmsRegistrationSub::where('id', $id)
            ->where('form_unique_id', '=', $request->screen_unique_id)
            ->firstOrFail();


        $firm_sublist->update([
            'proof_type' => $request->proof_type,
            'aadhar_licence' => $request->proof_value,
            'attached_proof'   => $attached_proof
        ]);


        return response()->json([
            'sublist updated successfully',
            'firm_sublist' => $firm_sublist,
            'proof_type' => $firm_sublist->proof_type,
            'aadhar_licence' => $firm_sublist->aadhar_licence,
            'attached_proof' => $firm_sublist->attached_proof
        ]);
    }




    public function destroy($id)
    {
        $firm = FirmsRegistration::findOrFail($id);
        // $firm->delete();
        $firm->is_delete = 1;
        $firm->save();
        return redirect()->route('firms_registration_list')->with('success', 'Firm deleted successfully');
    }


    public function sublist_edit($formUniqueId)
    {
        $sublistItem = FirmsRegistrationSub::findOrFail($formUniqueId);
        // Return a view for editing with the current sublist data
        return view('sublist.edit', compact('sublistItem'));
    }

    public function deleteSublistItem($form_unique_id): mixed
    {
        // Find the sublist item by its unique ID
        $sublistItem = FirmsRegistrationSub::where('form_unique_id', $form_unique_id)->first();

        // Check if the sublist item exists
        if (!$sublistItem) {
            return response()->json(['message' => 'Sublist item not found.'], 404);
        }

        // Set the is_delete field to 1 (soft delete)
        $sublistItem->is_delete = 1;

        // Save changes to the database and check for success
        if ($sublistItem->save()) {
            return response()->json(['message' => 'Sublist item deleted successfully.']);
        } else {
            return response()->json(['message' => 'Failed to delete sublist item.'], 500);
        }
    }
    // FirmController.php
    public function deleteDocument($id)
    {
        // Find the document in the sublist by ID
        $sublist = Sublist::findOrFail($id);

        // Delete the file from the uploads directory if it exists
        $filePath = public_path('uploads/firms_registration/' . $sublist->attached_proof);
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }

        // Delete the record from the database
        $sublist->delete();

        // Return a response or redirect back with a success message
        return response()->json(['success' => 'Document deleted successfully']);
    }
}