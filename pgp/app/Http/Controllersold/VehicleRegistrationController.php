<?php

namespace App\Http\Controllers;
use DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

class VehicleRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('vehicle_registration as vr')
                ->select(
                    'vr.vech_reg_no',
                    'vr.vehicle_category',
                    DB::raw('(SELECT cc.cargo
                FROM cargo_creation cc
                WHERE cc.unique_id = vr.vehicle_type) as vehicle_type'),
                    DB::raw('(SELECT vc.vehicle_type
                FROM vehicle_creation vc
                WHERE vc.unique_id = vr.vehicle_category) as vehicle_category'),
                    DB::raw('(SELECT vc.vehicle_type
                      FROM vehicle_creation vc
                      WHERE vc.unique_id = vr.vehicle_category) as status'),
                    'vr.entry_date',
                    'vr.screenUniqueId',
                    DB::raw('(SELECT valid_up_to FROM vehicle_registration_sub
                      WHERE vehicle_registration_sub.screenUniqueId = vr.screenUniqueId
                      AND certificate_type = "Fitness" AND is_delete=0) as fitness'),
                    DB::raw('(SELECT valid_up_to FROM vehicle_registration_sub
                      WHERE vehicle_registration_sub.screenUniqueId = vr.screenUniqueId
                      AND certificate_type = "Insurance" AND is_delete=0) as insurance'),
                    DB::raw('(SELECT valid_up_to FROM vehicle_registration_sub
                      WHERE vehicle_registration_sub.screenUniqueId = vr.screenUniqueId
                      AND certificate_type = "PUC" AND is_delete=0) as puc')
                )
                ->where('vr.is_delete', 0) // Filter out deleted vehicles
                ->orderBy('vr.id', 'DESC');

            // Apply filters
            if ($request->has('from_date') && $request->from_date != '') {
                $query->where('vr.entry_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $query->where('vr.entry_date', '<=', $request->to_date);
            }
            if ($request->has('vehicle_no') && $request->vehicle_no != '') {
                $query->where('vr.vech_reg_no', 'like', '%' . $request->vehicle_no . '%');
            }
            if ($request->has('vehicle_type') && $request->vehicle_type != '') {
                $query->where('vr.vehicle_type', 'like', '%' . $request->vehicle_type . '%');
            }
            if ($request->has('vehicle_category') && $request->vehicle_category != '') {
                $query->where('vr.vehicle_category', 'like', '%' . $request->vehicle_category . '%');
            }

            $vehicles = $query->get();

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
                    $editUrl = route('vehicle_registration.edit', $row->screenUniqueId);
                    return '
                    <a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                    <button class="delete-btn text-danger" data-id="' . $row->screenUniqueId . '" title="Delete" style="background:none; border:none;">
                        <i class="fas fa-trash-alt"></i>
                    </button>';
                })
                ->rawColumns(['action', 'fitness', 'insurance', 'puc'])
                ->make(true);
        }

        return view('content.vehicle_registration.index');
    }

    public function sublistindex(Request $request)
    {
        // Retrieve the sublist data;
        $sublist = VehicleDocument::where('is_delete', 0)->where('screenUniqueId', $request->screenUniqueId)->get();

        // Return the data as a JSON response
        return response()->json($sublist);
    }
    private function formatDate($date)
    {
        if ($date) {
            // Convert the date string to a DateTime object
            $validUpToDate = \Carbon\Carbon::parse($date);
            // Compare with the current date
            if ($validUpToDate->isPast()) {
                // Return formatted date in red if it's past
                return '<span style="color:red;">' . $validUpToDate->format('Y-m-d') . '</span>';
            } else {
                return '<span style="color:green;">' . $validUpToDate->format('Y-m-d');
            }
            // Return normal formatted date if it's not past

        }
        return ''; // Return an empty string if no date
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.vehicle_registration.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function stores(Request $request)
    {
        // Validate the request
        $request->validate([
            'certificate_type' => 'required|string|max:255',
            'vech_reg_no' => 'nullable|string|max:255',
            'certificate_no' => 'nullable|string|max:255',
            'valid_up_to' => 'nullable|string|max:255',
            'screenUniqueId' => 'required|string|max:255',
            'attached_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);

        // Check if a record with the same screenUniqueId and certificate_type exists
        $vehicleDocument = VehicleDocument::where('screenUniqueId', '=', $request->screenUniqueId)
            ->where('certificate_type', '=', $request->certificate_type)
            ->where('is_delete', '=', 0)
            ->first();
        // dd($vehicleDocument);
        // Prepare the data for creating/updating the record
        $data = [
            'screenUniqueId' => $request->screenUniqueId,
            'certificate_type' => $request->certificate_type,
            'vehicle_id' => $request->vech_reg_no,
            'valid_up_to' => $request->valid_up_to,
            'certificate_no' => $request->certificate_no,
        ];

        // Handle file upload if provided
        if ($request->hasFile('attached_proof')) {
            $attached_proof = $request->file('attached_proof');
            $attached_proofs = time() . '-' . $attached_proof->getClientOriginalName();
            $attached_proof->move(public_path('public/uploads/vehicle_registration'), $attached_proofs);
            $data['attached_proof'] = $attached_proofs; // Add file path to the data array
        }

        if ($vehicleDocument) {
            // If record exists, update it
            $vehicleDocument->update($data);
            return response()->json(['message' => 'Vehicle document updated successfully.', 'vehicle' => $vehicleDocument]);
        } else {
            // If record does not exist, create a new one
            $vehicleDocument = VehicleDocument::create(array_merge($data, [
                'unique_id' => unique_id(), // Generate a unique ID only for new records
            ]));
            return response()->json(['message' => 'Vehicle document registered successfully.', 'vehicle' => $vehicleDocument]);
        }
    }


    public function store(Request $request)
    {
        // dd($request);
        $currentDate = now()->format('Y-m-d');
        $prefix = 'VEH';
        // $currentYear = date('Y');
        $numericCode = date('Y'); // Use your existing function
        $lastNumber = $this->getEmployeeId();

        $register_no = $this->generateEmployeeID($prefix, $numericCode, $lastNumber);
        $port = 1;
        // Validate the request
        $request->validate([
            'vech_reg_no' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'vehicle_category' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'screenUniqueId' => 'required|string|max:255',
            'login_id' => 'required|string|max:255',
            'password' => ['nullable', 'string', 'min:8', 'max:1000', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/', 'confirmed'],
            'password_confirmation' => 'nullable|string|max:1000',
            'acc_year' => 'required|string|max:1000',
            'rf_id' => 'required|string|max:1000',
            'Vehicle_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',

            // Example: 10-10-2024


        ]);

        $password = $request->password ? Hash::make($request->password) : null;
        $confirm_password = $request->password_confirmation ? Hash::make($request->password_confirmation) : null;

        if ($request->hasFile('Vehicle_image')) {
            $vehicleImage = $request->file('Vehicle_image');
            $imageName = time() . '-' . $vehicleImage->getClientOriginalName();
            $vehicleImage->move(public_path('uploads/vehicle_registration'), $imageName);
            $Vehicle_image = $imageName; // Store the file path for the database
        }
        // dd($Vehicle_image);
        $vehicle = Vehicle::create([

            'unique_id' => unique_id(),
            'screenUniqueId' => $request->screenUniqueId,
            'vehicle_type' => $request->status,
            'vehicle_category' => $request->vehicle_category,
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'contact_number' => $request->contact_number,
            'user_id' => $request->login_id,
            'rf_id' => $request->rf_id,
            'port' => $port,
            'password' => $password,
            'confirm_password' => $confirm_password,
            'acc_year' => $request->acc_year,
            'entry_date' => $currentDate, // Example: 10-10-2024
            'vehicle_serial_no' => $register_no,
            'Vehicle_image' => $Vehicle_image,

        ]);
        // dd($Vehicle_image);
        return response()->json(['message' => 'Vehicle registered successfully.', 'vehicle' => $vehicle]);
    }


    /**
     * Show the form for editing the specified resource.
     */


    public function edit($screenUniqueId)
    {
        $vehicle = Vehicle::where('screenUniqueId', $screenUniqueId)->first();

        $vehicleDocument_list = VehicleDocument::where('screenUniqueId', $screenUniqueId)->where('is_delete', 0)->get();

        return view('content.vehicle_registration.edit', compact('vehicle', 'vehicleDocument_list'));
    }



    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'vech_reg_no' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'login_id' => 'nullable|string|max:255',
            'password' => ['nullable', 'string', 'min:8', 'max:1000', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/', 'confirmed',],
            'confirm_password' => 'nullable|string|max:1000',
            'acc_year' => 'required|string|max:1000',
            'rf_id' => 'required|string|max:1000',
            'Vehicle_image' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:250',

        ]);

        $dataToUpdate = [
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'status' => $request->status,
            'contact_number' => $request->contact_number,
            'rf_id' => $request->rf_id,
            'user_id' => $request->login_id,
            'acc_year' => $request->acc_year,
        ];

        if ($request->hasFile('Vehicle_image')) {
            $vehicleImage = $request->file('Vehicle_image');
            $imageName = time() . '-' . $vehicleImage->getClientOriginalName();
            $vehicleImage->move(public_path('public/uploads/vehicle_registration'), $imageName);
            $dataToUpdate['Vehicle_image'] = $imageName;
        }
        // Check if password fields are provided
        if ($request->filled('password') && $request->filled('confirm_password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
            $dataToUpdate['confirm_pass'] = Hash::make($request->confirm_password);
        }

        // Update the vehicle record
        $vehicle->update($dataToUpdate);



        return response()->json(['message' => 'Vehicle registration updated successfully', 'vehicle_registration' => $vehicle]);
    }



    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        // Assuming you have a model called Vehicle
        $vehicle = Vehicle::where('screenUniqueId', $id)->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Vehicle not found'], 404);
        }

        // Soft delete by setting is_delete flag
        $vehicle->is_delete = 1;
        $vehicle->save();

        return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully']);
    }

    // public function destroy($id)
    // {
    //     // Assuming you have a model called VehicleRegistration
    //     $vehicle = Vehicle::where('screenUniqueId', $id)->first();

    //     if ($vehicle) {
    //         $vehicle->delete(); // Or soft delete if you have that implemented
    //         return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully']);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Vehicle not found'], 404);
    // }

    public function destroys($id)
    {
        $vehicle = VehicleDocument::find($id);

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

        $document = VehicleDocument::findOrFail($id);
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
        $lastAssetId = DB::table('vehicle_registration') // Replace with your actual table name
            ->where('is_delete', '=', 0)
            ->max('vehicle_serial_no');

        if ($lastAssetId) {
            return (int) substr($lastAssetId, -5) + 1; // Increment the last number
        } else {
            return 1; // Start from 1 if no asset exists
        }
    }

    // Helper method to generate asset ID
    public function generateEmployeeID($prefix = 'VEH', $numericCode, $lastNumber)
    {
        $newNumber = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        return sprintf('%s%s%s', $prefix, $numericCode, $newNumber);
    }




    public function getVehicleCategory($vehicleTypeId)
    {
        $vehicleCategories = DB::table('vehicle_creation')
            ->select('unique_id', 'vehicle_type')
            ->where(['is_delete' => 0, 'is_active' => 1, 'cargo' => $vehicleTypeId])
            ->orderBy('id', 'ASC')
            ->get();
        // dd( $vehicleCategories);
        return response()->json($vehicleCategories);

        // dd($vehicleCategories);
    }

}
