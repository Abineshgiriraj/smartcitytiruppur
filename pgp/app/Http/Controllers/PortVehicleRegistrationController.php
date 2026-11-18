<?php

namespace App\Http\Controllers;

use App\Models\CargoCreation;
use DataTables;

use App\Models\PortVehicle;
use App\Models\PortVehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class PortVehicleRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('port_vehicle_registration as pvr')
                ->select(
                    'pvr.entry_date',
                    'pvr.vech_reg_no',
                    DB::raw('(SELECT cargo
                        FROM cargo_creation
                        WHERE cargo_creation.unique_id COLLATE utf8mb4_unicode_ci = pvr.vehicle_type) as vehicle_type'),
                    DB::raw('(SELECT vehicle_type
                        FROM vehicle_creation
                        WHERE vehicle_creation.unique_id COLLATE utf8mb4_unicode_ci = pvr.vehicle_category) as vehicle_category'),
                    'pvr.entry_date',
                    'pvr.screenUniqueId',
                    DB::raw('(SELECT valid_up_to
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                          AND certificate_type = "Fitness" AND is_delete = 0) as fitness'),
                    DB::raw('(SELECT valid_up_to
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                          AND certificate_type = "Insurance" AND is_delete = 0) as insurance'),
                    DB::raw('(SELECT valid_up_to
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                          AND certificate_type = "PUC" AND is_delete = 0) as puc')
                )
                ->where('pvr.is_delete', 0)
                ->orderBy('id','desc');

            // Apply filters
            if ($request->has('from_date') && $request->from_date != '') {
                $query->where('pvr.entry_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $query->where('pvr.entry_date', '<=', $request->to_date);
            }
            if ($request->has('vehicle_no') && $request->vehicle_no != '') {
                $query->where('pvr.vech_reg_no', 'like', '%' . $request->vehicle_no . '%');
            }
            if ($request->has('vehicle_type') && $request->vehicle_type != '') {
                $query->where('pvr.vehicle_type', 'like', '%' . $request->vehicle_type . '%');
            }
            if ($request->has('vehicle_category') && $request->vehicle_category != '') {
                $query->where('pvr.vehicle_category', 'like', '%' . $request->vehicle_category . '%');
            }

            $vehicles = $query->get(); // Change to get() to execute the query

            return Datatables::of($vehicles)
                ->addIndexColumn()
                ->addColumn('fitness', function ($row) {
                    $fitnessDate = $row->fitness;
                    return $this->formatDate($fitnessDate);
                })
                ->addColumn('insurance', function ($row) {
                    $insuranceDate = $row->insurance;
                    return $this->formatDate($insuranceDate);
                })
                ->addColumn('puc', function ($row) {
                    $pucDate = $row->puc;
                    return $this->formatDate($pucDate);
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('port_vehicle_registration.edit', $row->screenUniqueId);
                    return '
                        <a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                        <button class="delete-btn text-danger" data-id="' . $row->screenUniqueId . '" title="Delete" style="background:none; border:none;">
                            <i class="fas fa-trash-alt"></i>
                        </button>';
                })
                ->rawColumns(['action', 'fitness', 'insurance', 'puc'])
                ->make(true);
        }

        return view('content.port_vehicle_registration.index');
    }

    /**
     * Helper function to format the date with conditional styling.
     */
    private function formatDate($date)
    {
        if ($date) {
            // Convert the date string to a DateTime object
            $validUpToDate = \Carbon\Carbon::parse($date);
            // Compare with the current date
            if ($validUpToDate->isPast()) {
                // Return formatted date in red if it's past
                return '<span style="color:red;font-weight:bold;">' . $validUpToDate->format('Y-m-d') . '</span>';
            }
            // Return normal formatted date if it's not past
            return '<span style="color:green;">' . $validUpToDate->format('Y-m-d') . '</span>';
        }
        return ''; // Return an empty string if no date
    }

    /**
     * Show the form for creating a new resource.
     */

    public function sublistindex(Request $request)
    {

        // Retrieve the sublist data;
        $sublist = PortVehicleDocument::where('is_delete', 0)->where('screenUniqueId', $request->screenUniqueId)->get();

        // Return the data as a JSON response
        return response()->json($sublist);
    }

    public function create()
    {
        return view('content.port_vehicle_registration.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function stores(Request $request)
    {
        // Validate the request
        $request->validate([
            'certificate_type' => 'required|string|max:255',
            'vech_reg_no' => 'required|string|max:255',
            'certificate_no' => 'nullable|string|max:255',
            'valid_up_to' => 'nullable|string|max:255',
            'screenUniqueId' => 'required|string|max:255',
            'attached_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);

        // Find existing document
        $vehicleDocument = PortVehicleDocument::where('screenUniqueId', $request->screenUniqueId)
            ->where('certificate_type', $request->certificate_type)
            ->where('is_delete', 0)
            ->first();

        // Prepare the data for creating/updating the record
        $data = [
            'screenUniqueId' => $request->screenUniqueId,
            'certificate_type' => $request->certificate_type,
            'vehicle_id' => $request->vech_reg_no,
            'valid_up_to' => $request->valid_up_to,
            'certificate_no' => $request->certificate_no,
        ];

        // Check if a file is uploaded
        if ($request->hasFile('attached_proof')) {
            $attached_proof = $request->file('attached_proof');
            $attached_proof_name = time() . '-' . $attached_proof->getClientOriginalName();
            $attached_proof->move('uploads/port_vehicle_registration', $attached_proof_name);

            // Store the file path in data
            $data['attached_proof'] = $attached_proof_name;

            // If updating, delete the old file if it exists
            if ($vehicleDocument && $vehicleDocument->attached_proof) {
                $oldFilePath = 'uploads/port_vehicle_registration/' . $vehicleDocument->attached_proof;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        if ($vehicleDocument) {
            // Update existing record
            $vehicleDocument->update($data);
            return response()->json(['message' => 'Vehicle document updated successfully.', 'vehicle' => $vehicleDocument]);
        } else {
            // Create a new record if it does not exist
            $vehicleDocument = PortVehicleDocument::create(array_merge($data, [
                'unique_id' => unique_id(), // Generate unique ID only for new records
            ]));
            //  dd($vehicleDocument);
            return response()->json(['message' => 'Vehicle document registered successfully.', 'vehicle' => $vehicleDocument]);
        }
    }

    // if ($vehicleDocument) {
    //     // If record exists, update it
    //     $vehicleDocument->update($data);
    //     return response()->json(['message' => 'Port Vehicle document updated successfully.', 'vehicle' => $vehicleDocument]);
    // } else {
    //     // Create the PortVehicleDocument record
    //     $vehicle = PortVehicleDocument::create([
    //         'unique_id' => unique_id(),
    //         'screenUniqueId' => $request->screenUniqueId,
    //         'certificate_type' => $request->certificate_type,
    //         'vehicle_id' => $request->vech_reg_no,
    //         'valid_up_to' => $request->valid_up_to,
    //         'certificate_no' => $request->certificate_no,
    //         'attached_proof' => $attached_proof,
    //     ]);

    //     return response()->json(['message' => 'PortVehicle registered successfully.', 'vehicle' => $vehicle]);
    // }
    // }

    public function VechRegID($vechileType)
    {

        $getvehicleType = CargoCreation::where('unique_id', $vechileType)->first('cargo');

        $nameParts = explode(' ', $getvehicleType->cargo);

        // If there's only one word, just take the first letter
        if (count($nameParts) === 1) {
            $getvalue = strtoupper(substr($nameParts[0], 0, 1)); // Only the first letter of the single word
        }

        $prefix =  $getvalue;
        $new_vaiable = 'AP';
        $year = date('Y');
        $month = date('m');
        $lastvechicle = PortVehicle::where('app_prefix_id', 'like', "$new_vaiable$prefix$year%")
            ->orderBy('app_id', 'desc')
            ->first();

        $lastFirmNumber = $lastvechicle ? (int) substr($lastvechicle->app_id, -5) : 0;
        $newFirmId = sprintf("%s%s%s%05d", $new_vaiable, $prefix, $year, $lastFirmNumber + 1);

        $app_id_prefix = substr($newFirmId, 0, 7);
        $app_id = substr($newFirmId, -5);
        return ['appIdPrefix' => $app_id_prefix, 'appId' => $app_id];
    }




    public function store(Request $request)
    {

        $prefix = 'PVR';
        // $currentYear = date('Y');
        $numericCode = date('Y'); // Use your existing function
        $lastNumber = $this->getEmployeeId();

        $register_no = $this->generateEmployeeID($prefix, $numericCode, $lastNumber);
        // dd($register_no);
        $vehicleType = $request->vehicle_type;
        $specificId = '6711f18659aad63646';
        // Validate the request
        $request->validate([
            // 'vech_reg_no' => 'required|string|max:255',
            'vech_reg_no' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->where(function ($query) {
                    // Ensure the 'is_delete' field is 0 (not deleted)
                    return $query->where('is_delete', 0);
                }),
            ],
            'owner_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'vehicle_category' => 'required|string|max:255',
            // 'contact_number' => 'required|string|max:20',
            'contact_number' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->where(function ($query) {
                    // Ensure the 'is_delete' field is 0 (not deleted)
                    return $query->where('is_delete', 0);
                }),
            ],
            'screenUniqueId' => 'required|string|max:255',
            //     'login_id' => 'required|string|max:255',
            //     'password' => [
            //       'nullable',
            //       'string',
            //       'min:8',
            //       'max:1000',
            //       'regex:/[A-Z]/', // At least one uppercase letter
            //       'regex:/[a-z]/', // At least one lowercase letter
            //       'regex:/[0-9]/', // At least one number
            //       'regex:/[@$!%*?&#]/', // At least one special character
            //       'confirmed' // Matches 'password_confirmation' field
            //   ],

            //   'password_confirmation' => 'nullable|string|max:1000',
            // 'rf_id' => 'required|string|max:100',
            'acc_year' => 'required|string|max:1000'
        ]);

        if ($vehicleType === $specificId) {
            $rules['chassis_no'] = 'required|string|max:50';
            $rules['model_name'] = 'required|string|max:50';
        }
        $entry_date = now()->format('Y-m-d');
        // $password = $request->password;
        // $confirm_password = $request->password_confirmation;
        // $hashedPassword = Hash::make($password);
        // $hashedConfirmPassword = Hash::make($confirm_password);

        // dd($hashedConfirmPassword, $hashedPassword);


        $vech_id  = $this->VechRegID($request->vehicle_type);



        $vehicle = PortVehicle::create([
            'unique_id' => unique_id(),
            'screenUniqueId' => $request->screenUniqueId,
            'app_prefix_id' =>    $vech_id['appIdPrefix'],
            'app_id' =>    $vech_id['appId'],

            'vehicle_type' => $request->vehicle_type,
            'vehicle_category' => $request->vehicle_category,
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'contact_number' => $request->contact_number,
            'model_name' => $request->model_name,
            'chassis_no' => $request->chassis_no,
            // 'user_id' => $request->login_id,
            // 'password' => $hashedPassword,
            // 'confirm_pass' => $hashedConfirmPassword,
            'vehicle_id' => $register_no,
            // 'rf_id' => $request->rf_id,
            'acc_year' => $request->acc_year,
            'entry_date' => $entry_date,
        ]);

        return response()->json(['message' => 'PortVehicle registered successfully.', 'vehicle' => $vehicle]);
    }


    public function getVehicleCategory($vehicleTypeId)
    {
        $vehicleCategories = DB::table('vehicle_creation')
            ->select('unique_id', 'vehicle_type')
            ->where(['is_delete' => 0, 'is_active' => 1, 'cargo' => $vehicleTypeId])
            ->orderBy('id', 'ASC')
            ->get();

        $vehicleDocument = DB::table('document_master')
            ->select('unique_id', 'type_of_proof')
            ->where(['is_delete' => 0, 'is_active' => 1, 'category_type' => $vehicleTypeId])
            ->orderBy('id', 'ASC')
            ->get();


        // dd( $merged_data);
        return response()->json([
            'vehicleCategories' => $vehicleCategories,
            'vehicleDocument' => $vehicleDocument
        ]);
        // dd($vehicleCategories);
    }


    public function edit($screenUniqueId)
    {

        // Retrieve the vehicle by unique_id instead of the default primary key
        $vehicle = PortVehicle::where('screenUniqueId', $screenUniqueId)->first();

        // Assuming you want to retrieve documents based on vehicle's unique_id as well
        $vehicleDocument_list = PortVehicleDocument::where('screenUniqueId', $screenUniqueId)->where('is_delete', 0)->get();


        return view('content.port_vehicle_registration.edit', compact('vehicle', 'vehicleDocument_list'));
    }



    public function update(Request $request, $id)
    {
        $vehicle = PortVehicle::findOrFail($id);
        $vehicleType = $request->vehicle_type;
        $specificId = '6711f18659aad63646';

        // Define base validation rules
        $rules = [
            'vech_reg_no' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->ignore($vehicle->id)->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'owner_name' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_category' => 'nullable|string|max:255',
            'contact_number' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->ignore($vehicle->id)->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'acc_year' => 'nullable|string|max:1000',
        ];

        // If specific vehicle type is selected, add extra validation for model_name and chassis_no
        if ($vehicleType === $specificId) {
            $rules['model_name'] = 'required|string|max:50';
            $rules['chassis_no'] = 'required|string|max:50';
        }

        // Validate the request
        $request->validate($rules);

        // Prepare data to update the vehicle record
        $dataToUpdate = [
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_category' => $request->vehicle_category,
            'contact_number' => $request->contact_number,
            'acc_year' => $request->acc_year,
            'model_name' => $request->model_name,
            'chassis_no' => $request->chassis_no,
        ];

        // Check if password fields are provided and hash them
        if ($request->filled('password') && $request->filled('password_confirmation')) {
            $dataToUpdate['password'] = Hash::make($request->password);
            $dataToUpdate['confirm_pass'] = Hash::make($request->password_confirmation);
        }

        // Update the vehicle record
        $vehicle->update($dataToUpdate);

        return response()->json(['message' => 'PortVehicle registration updated successfully', 'port_vehicle_registration' => $vehicle]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Assuming you have a model called Vehicle
        $vehicle = PortVehicle::where('screenUniqueId', $id)->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Port Vehicle not found'], 404);
        }

        // Soft delete by setting is_delete flag
        $vehicle->is_delete = 1;
        $vehicle->save();

        return response()->json(['success' => true, 'message' => 'Port Vehicle deleted successfully']);
    }
    public function destroys($id)
    {
        $vehicle = PortVehicleDocument::find($id);

        if ($vehicle) {
            // Optionally delete the associated file here

            $vehicle->delete();

            return response()->json(['message' => 'Document deleted successfully.']);
        }

        return response()->json(['message' => 'Document not found.'], 404);
    }

    public function markAsDeleted(Request $request, $id)
    {
        $request->validate([
            'is_delete' => 'required|boolean',
        ]);

        $document = PortVehicleDocument::findOrFail($id);
        $document->is_delete = $request->is_delete;
        $document->save();

        return response()->json(['success' => true]);
    }

    public function extractNumbers($string)
    {
        return preg_replace('/\D/', '', $string);
    }

    // Helper method to get the last asset number
    public function getEmployeeId()
    {
        $lastAssetId = DB::table('port_vehicle_registration') // Replace with your actual table name
            // ->where('hostel_id', $hostel_id)
            ->max('vehicle_id');

        if ($lastAssetId) {
            return (int) substr($lastAssetId, -5) + 1; // Increment the last number
        } else {
            return 1; // Start from 1 if no asset exists
        }
    }

    // Helper method to generate asset ID
    public function generateEmployeeID($prefix = 'PVR', $numericCode, $lastNumber)
    {
        $newNumber = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        return sprintf('%s%s%s', $prefix, $numericCode, $newNumber);
    }
}
