<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\EmployeeDesignation;

class EmployeeDesignationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $employee_designation = DB::table('employee_designation')
                ->select('id', 'designation', 'description')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($employee_designation)
                ->addIndexColumn()


                // In your controller's index method, update the addColumn for actions
                ->addColumn('action', function ($row) {
                    $editUrl = route('employee_designation_edit', $row->id);

                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</a>';
                    return $btn;
                    
                })


                ->make(true);
        }
        return view('content.employee_designation.list');
    }

    public function add()
    {
        return view('content.employee_designation.form');
    }

    public function edit($id)
    {
        $employeeDesignation = EmployeeDesignation::findOrFail($id);
        return view('content.employee_designation.edit', compact('employeeDesignation'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        $exists = EmployeeDesignation::where('designation', $request->designation)
            ->exists();
        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $employeeDesignation = EmployeeDesignation::create([
            'unique_id' => unique_id(),
            'designation' => $request->designation,
            'description' => $request->description,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'Designation added successfully!', 'Designation' => $employeeDesignation]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $employeeDesignation = EmployeeDesignation::findOrFail($id);

        // Check if the designation already exists in other entries
        $exists = EmployeeDesignation::where('designation', $request->designation)
            ->where('id', '!=', $id) // Exclude the current entry
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Designation already exists.'], 422);
        }

        // Update the designation and other fields
        $employeeDesignation->designation = $request->designation;
        $employeeDesignation->description = $request->description;
        $employeeDesignation->is_active = $request->status; // Assuming you're using this field
        $employeeDesignation->save();

        return response()->json(['success' => 'Designation updated successfully!']);
    }


    public function destroy($id)
    {
        $acc_year = EmployeeDesignation::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('employee_designation')->with('success', 'Designation deleted successfully!');
    }

}
