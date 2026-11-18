<?php

namespace App\Http\Controllers;
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
                    'pvr.vech_reg_no',
                    'pvr.vehicle_type',
                    'pvr.entry_date',
                    'pvr.screenUniqueId',
                    DB::raw('(SELECT valid_up_to FROM port_vehicle_registration_sub
                           WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                           AND certificate_type = "Fitness" AND is_delete = 0) as fitness'),
                    DB::raw('(SELECT valid_up_to FROM port_vehicle_registration_sub
                           WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                           AND certificate_type = "Insurance" AND is_delete = 0) as insurance'),
                    DB::raw('(SELECT valid_up_to FROM port_vehicle_registration_sub
                           WHERE port_vehicle_registration_sub.screenUniqueId = pvr.screenUniqueId
                           AND certificate_type = "PUC" AND is_delete = 0) as puc')
                )->where('pvr.is_delete', 0); // Add this line to filter out deleted vehicles
            // Apply filters
            if ($request->has('from_date') && $request->from_date != '') {
                $query->where('pvr.entry_date', '>=', $request->from_date . ' 00:00:00');
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $query->where('pvr.entry_date', '<=', $request->to_date . ' 23:59:59');
            }
            if ($request->has('vehicle_no') && $request->vehicle_no != '') {
                $query->where('pvr.vech_reg_no', 'like', '%' . $request->vehicle_no . '%');
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
                return $validUpToDate->format('Y-m-d');
            }
            // Return normal formatted date if it's not past
            return $validUpToDate->format('Y-m-d');
        }
        return ''; // Return an empty string if no date
    }

    /**
     * Show the form for creating a new resource.
     */
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
            // 'certificate_no' => 'nullable|string|max:255',
            'certificate_no' => [
                'nullable',
                'string'
            ],
            'valid_up_to' => 'nullable|string|max:255',
            'screenUniqueId' => 'required|string|max:255',
            'attached_proof' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
        ]);


        $vehicleDocument = PortVehicleDocument::where('screenUniqueId', '=', $request->screenUniqueId)
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
        // Create an array to hold file paths if files are uploaded
        $filePaths = [];

        if ($request->hasFile('attached_proof')) {
            $attached_proof = $request->file('attached_proof');
            $attached_proofs = time() . '-' . $attached_proof->getClientOriginalName();
            $attached_proof->move(public_path('uploads/port_vehicle_registration'), $attached_proofs);
            $attached_proof = $attached_proofs;
        }
        if ($vehicleDocument) {
            // If record exists, update it
            $vehicleDocument->update($data);
            return response()->json(['message' => 'Port Vehicle document updated successfully.', 'vehicle' => $vehicleDocument]);
        } else {
            // Create the PortVehicleDocument record
            $vehicle = PortVehicleDocument::create([
                'unique_id' => unique_id(),
                'screenUniqueId' => $request->screenUniqueId,
                'certificate_type' => $request->certificate_type,
                'vehicle_id' => $request->vech_reg_no,
                'valid_up_to' => $request->valid_up_to,
                'certificate_no' => $request->certificate_no,
                'attached_proof' => $attached_proof,
            ]);

            return response()->json(['message' => 'PortVehicle registered successfully.', 'vehicle' => $vehicle]);
        }
    }


    public function store(Request $request)
    {

        $prefix = 'PVR';
        // $currentYear = date('Y');
        $numericCode = date('Y'); // Use your existing function
        $lastNumber = $this->getEmployeeId();

        $register_no = $this->generateEmployeeID($prefix, $numericCode, $lastNumber);
        // dd($register_no);

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
            'login_id' => 'required|string|max:255',
            'password' => 'required|string|max:1000',
            'confirm_password' => 'required|string|max:1000',
            'rf_id' => 'required|string|max:100',
            'acc_year' => 'required|string|max:1000'
        ]);

        $password = $request->password;
        $confirm_password = $request->confirm_password;
        $hashedPassword = Hash::make($password);
        $hashedConfirmPassword = Hash::make($confirm_password);

        // dd($hashedConfirmPassword, $hashedPassword);

        $vehicle = PortVehicle::create([
            'unique_id' => unique_id(),
            'screenUniqueId' => $request->screenUniqueId,
            'vehicle_type' => $request->vehicle_type,
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'contact_number' => $request->contact_number,
            'user_id' => $request->login_id,
            'password' => $hashedPassword,
            'confirm_pass' => $hashedConfirmPassword,
            'vehicle_id' => $register_no,
            'rf_id' => $request->rf_id,
            'acc_year' => $request->acc_year,
            'entry_date' => now()
        ]);

        return response()->json(['message' => 'PortVehicle registered successfully.', 'vehicle' => $vehicle]);
    }


    /**
     * Show the form for editing the specified resource.
     */

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

        $request->validate([
            // 'vech_reg_no' => 'nullable|string|max:255',
            'vech_reg_no' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->ignore($vehicle->id)->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'owner_name' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            // 'contact_number' => 'nullable|string|max:20',
            'contact_number' => [
                'required',
                'string',
                Rule::unique('port_vehicle_registration')->ignore($vehicle->id)->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'login_id' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:1000',
            'confirm_password' => 'nullable|string|max:1000',
            'acc_year' => 'nullable|string|max:1000',
            'rf_id' => 'nullable|string|max:1000',
        ]);

        // $password = $request->password;
        // $confirm_password  = $request->confirm_password;
        // $hashedPassword = Hash::make($password);
        // $hashedConfirmPassword = Hash::make($confirm_password);

        // $vehicle->update([
        //     $vehicle->vech_reg_no = $request->vech_reg_no,
        //     $vehicle->owner_name = $request->owner_name,
        //     $vehicle->vehicle_type = $request->vehicle_type,
        //     $vehicle->contact_number = $request->contact_number,
        //     $vehicle->user_id = $request->login_id,
        //     $vehicle->password = $hashedPassword,
        //     $vehicle->confirm_pass = $hashedConfirmPassword,
        //     $vehicle->acc_year = $request->acc_year
        // ]);

        // $vehicle->update([
        //     'vech_reg_no' => $request->vech_reg_no,
        //     'owner_name' => $request->owner_name,
        //     'vehicle_type' => $request->vehicle_type,
        //     'contact_number' => $request->contact_number,
        //     'user_id' => $request->login_id,
        //     'password' => $hashedPassword,
        //     'confirm_pass' => $hashedConfirmPassword,
        //     'acc_year' => $request->acc_year
        // ]);

        $dataToUpdate = [
            'vech_reg_no' => $request->vech_reg_no,
            'owner_name' => $request->owner_name,
            'vehicle_type' => $request->vehicle_type,
            'contact_number' => $request->contact_number,
            'rf_id' => $request->rf_id,
            'user_id' => $request->login_id,
            'acc_year' => $request->acc_year,
        ];

        // Check if password fields are provided
        if ($request->filled('password') && $request->filled('confirm_password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
            $dataToUpdate['confirm_pass'] = Hash::make($request->confirm_password);
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
