<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\EmployeeRegistration;

use App\Models\EmployeeRegistrationDocument;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;

class EmployeeRegistrationController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('employee_registration as er')
                ->leftJoin('employee_designation', 'er.designation', '=', 'employee_designation.unique_id')
                ->leftJoin('employee_reg_doc_sub', 'er.screenUniqueId', '=', 'employee_reg_doc_sub.screenUniqueId')
                ->select(
                    'er.register_no',
                    'er.employee_id',
                    'er.fullname',
                    'employee_designation.designation as designations',
                    'er.aadhar_no',
                    'er.screenUniqueId',
                    'er.designation'
                )
                ->where('er.is_delete', 0);

            if ($request->has('designation') && $request->designation != '') {
                $query->where('er.designation', 'like', '%' . $request->designation . '%');
            }

            if ($request->has('id_no') && $request->id_no != '') {
                $query->where('employee_reg_doc_sub.proof_no', 'like', '%' . $request->id_no . '%');
            }

            // $query->groupBy('er.screenUniqueId');

            $employee_registration = $query->distinct()->get();

            return Datatables::of($employee_registration)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('employee_registration.edit', $row->screenUniqueId);
                    return '
                        <a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                        <button class="delete-btn text-danger" data-id="' . $row->screenUniqueId . '" title="Delete" style="background:none; border:none;">
                            <i class="fas fa-trash-alt"></i>
                        </button>';
                })
                ->make(true);
        }

        return view('content.employee_registration.index');
    }

    public function add()
    {
        return view('content.employee_registration.create');
    }


    public function edit($screenUniqueId)
    {

        // Retrieve the vehicle by unique_id instead of the default primary key
        $employeeregistration = EmployeeRegistration::where('screenUniqueId', $screenUniqueId)->first();

        // Assuming you want to retrieve documents based on employeeregistration's unique_id as well
        $employeeRegistrationDocument_list = EmployeeRegistrationDocument::where('screenUniqueId', $screenUniqueId)->where('is_delete', 0)->get();


        return view('content.employee_registration.edit', compact('employeeregistration', 'employeeRegistrationDocument_list'));
    }

    public function store(Request $request)
    {
        $currentDate = now()->format('Y-m-d');
        $prefix = 'EMP';
        // $currentYear = date('Y');
        $numericCode = date('Y'); // Use your existing function
        $lastNumber = $this->getEmployeeId();

        $register_no = $this->generateEmployeeID($prefix, $numericCode, $lastNumber);

        // Validate the request
        $request->validate([
            'fullName' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:1000',
            'dob' => 'nullable|string|max:1000',
            'blood_grp' => 'nullable|string|max:1000',
            'aadhaar_no' => 'nullable|string|max:1000',
            'email' => 'nullable|string|max:1000',
            'department' => 'nullable|string|max:1000',
            'designation' => 'nullable|string|max:1000',
            'licence_expire' => 'nullable|string|max:1000',
            'area' => 'nullable|string|max:1000',
            'rfid_no' => 'nullable|string|max:1000',
            'authority' => 'nullable|string|max:1000',
            'acc_year' => 'nullable|string|max:1000',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string|max:1000',
            'login_id' => 'nullable|string|max:1000',
            'password' => 'nullable|string|max:1000',
            'confirm_password' => 'nullable|string|max:1000'
        ]);

        $password = $request->password ? Hash::make($request->password) : null;
        $confirm_password = $request->confirm_password ? Hash::make($request->confirm_password) : null;

        $employeeregistration = EmployeeRegistration::create([
            'unique_id' => unique_id(),
            'screenUniqueId' => $request->screenUniqueId,
            'fullname' => $request->fullName,
            'employee_id' => $request->employee_id,
            'dob' => $request->dob,
            'blood_group' => $request->blood_grp,
            'aadhar_no' => $request->aadhaar_no,
            'email_id' => $request->email,
            'department' => $request->department,
            'designation' => $request->designation,
            'lic_exp_date' => $request->licence_expire,
            'area' => $request->area,
            'rfid_no' => $request->rfid_no,
            'register_no' => $register_no,
            'reg_date' => now(),
            'authority' => $request->authority,
            'acc_year' => $request->acc_year,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'user_id' => $request->login_id,
            'password' => $password,
            'confirm_password' => $confirm_password
        ]);

        return response()->json(['message' => 'Employee Registration added successfully.', 'Employee Registration' => $employeeregistration]);
    }
    public function stores(Request $request)
    {
        // Validate the request
        $request->validate([
            'idproof' => 'required|string|max:255',
            'proof_no' => [
                'required',
                'string',
                Rule::unique('employee_reg_doc_sub')->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'proof_attach' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048',
            'screenUniqueId' => 'required|string|max:255'
        ]);

        $employeeregistration = EmployeeRegistrationDocument::where('screenUniqueId', $request->screenUniqueId)
            ->where('proof_type', $request->idproof)
            ->where('is_delete', 0)
            ->first();

        $data = [
            'proof_type' => $request->idproof,
            'proof_no' => $request->proof_no,
        ];

        $proof_attach = null;

        if ($request->hasFile('proof_attach')) {
            $proof_attach = $request->file('proof_attach');
            $proof_attachs = time() . '-' . $proof_attach->getClientOriginalName();
            $proof_attach->move(public_path('uploads/employee_registration'), $proof_attachs);
            $proof_attach = $proof_attachs;
        }

        if ($employeeregistration) {
            // Update existing record
            $employeeregistration->update(array_merge($data, ['proof_name' => $proof_attach]));
            return response()->json(['success' => true, 'message' => 'Employee document updated successfully.', 'data' => $employeeregistration]);
        } else {
            // Create new record
            $employeeregistration = EmployeeRegistrationDocument::create(array_merge($data, [
                'unique_id' => unique_id(),
                'screenUniqueId' => $request->screenUniqueId,
                'proof_name' => $proof_attach,
            ]));
            return response()->json(['success' => true, 'message' => 'Employee Registration registered successfully.', 'data' => $employeeregistration]);
        }
    }

    public function sub_store(Request $request)
    {

        $request->validate([
            'login_id' => 'required|string|max:255',
            'pass_pin' => 'nullable|string|max:1000',
            'password' => 'nullable|string|max:1000',
            'confirm_pin' => 'nullable|string|max:1000',
            'hash_pass_pin' => 'nullable|string|max:1000'
        ]);

        // Find the existing employee registration by ID
        $employeeregistration = EmployeeRegistration::where('unique_id', $request->unique_id)->first();
        // dd($request);

        // Check if the record exists
        if (!$employeeregistration) {
            return response()->json(['error' => 'Employee Registration not found'], 404);
        }

        // Update the employee registration fields
        $employeeregistration->user_id = $request->login_id;
        $employeeregistration->pass_pin = $request->pass_pin;
        $employeeregistration->password = $request->password;
        $employeeregistration->confirm_password = $request->confirm_password;
        $employeeregistration->hash_pass_pin = $request->hash_pass_pin;

        // Save the changes
        $employeeregistration->save();

        return response()->json(['success' => 'Employee Registration added successfully!', 'Employee Registration' => $employeeregistration, 'unique_id' => $employeeregistration->unique_id]);
    }


    public function update(Request $request, $id)
    {
        $vehicle = EmployeeRegistration::findOrFail($id);

        $request->validate([
            'fullName' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:1000',
            'dob' => 'nullable|string|max:1000',
            'blood_grp' => 'nullable|string|max:1000',
            'aadhaar_no' => 'nullable|string|max:1000',
            'email' => 'nullable|string|max:1000',
            'department' => 'nullable|string|max:1000',
            'designation' => 'nullable|string|max:1000',
            'licence_expire' => 'nullable|string|max:1000',
            'area' => 'nullable|string|max:1000',
            'rfid_no' => 'nullable|string|max:1000',
            'authority' => 'nullable|string|max:1000',
            'acc_year' => 'nullable|string|max:1000',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string|max:1000',
            'login_id' => 'nullable|string|max:1000',
            'password' => 'nullable|string|max:1000',
            'confirm_password' => 'nullable|string|max:1000'
        ]);

        // dd($vehicle);

        $dataToUpdate = [
            'fullname' => $request->fullName,
            'employee_id' => $request->employee_id,
            'dob' => $request->dob,
            'blood_group' => $request->blood_grp,
            'aadhar_no' => $request->aadhaar_no,
            'email_id' => $request->email,
            'department' => $request->department,
            'designation' => $request->designation,
            'lic_exp_date' => $request->licence_expire,
            'area' => $request->area,
            'rfid_no' => $request->rfid_no,
            'authority' => $request->authority,
            'acc_year' => $request->acc_year,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'user_id' => $request->login_id
        ];

        // Check if password fields are provided
        if ($request->filled('password') && $request->filled('confirm_password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
            $dataToUpdate['confirm_pass'] = Hash::make($request->confirm_password);
        }

        // Update the vehicle record
        $vehicle->update($dataToUpdate);


        return response()->json(['message' => 'Employee registration updated successfully', 'vehicle' => $vehicle]);
    }

    public function destroys($id)
    {
        $vehicle = EmployeeRegistrationDocument::find($id);

        if ($vehicle) {
            // Optionally delete the associated file here

            $vehicle->delete();

            return response()->json(['message' => 'Document deleted successfully.']);
        }

        return response()->json(['message' => 'Document not found.'], 404);
    }

    public function destroy($id)
    {
        // Assuming you have a model called Vehicle
        $vehicle = EmployeeRegistration::where('screenUniqueId', $id)->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Soft delete by setting is_delete flag
        $vehicle->is_delete = 1;
        $vehicle->save();

        return response()->json(['success' => true, 'message' => 'Employee deleted successfully']);
    }


    public function extractNumbers($string)
    {
        return preg_replace('/\D/', '', $string);
    }

    // Helper method to get the last asset number
    public function getEmployeeId()
    {
        $lastAssetId = DB::table('employee_registration') // Replace with your actual table name
            // ->where('hostel_id', $hostel_id)
            ->max('register_no');

        if ($lastAssetId) {
            return (int) substr($lastAssetId, -5) + 1; // Increment the last number
        } else {
            return 1; // Start from 1 if no asset exists
        }
    }

    // Helper method to generate asset ID
    public function generateEmployeeID($prefix = 'EMP', $numericCode, $lastNumber)
    {
        $newNumber = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        return sprintf('%s%s%s', $prefix, $numericCode, $newNumber);
    }

    public function markAsDeleted(Request $request, $id)
    {
        $request->validate([
            'is_delete' => 'required|boolean',
        ]);

        $document = EmployeeRegistrationDocument::findOrFail($id);
        $document->is_delete = $request->is_delete;
        $document->save();

        return response()->json(['success' => true]);
    }

}
