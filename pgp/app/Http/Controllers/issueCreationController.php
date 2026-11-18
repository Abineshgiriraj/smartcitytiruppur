<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\acc_year;
use Illuminate\Validation\Rule;

class issueCreationController extends Controller
{

    // For DataTable
    public function index(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        if ($request->ajax()) {
            $data = acc_year::where('is_delete', 0)->latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('financeYearCreation.edit', $row->id) . '" class="text-primary">
            <i class="fas fa-edit"></i>
        </a>
        <a class="text-danger ml-2" onclick="confirmDelete(' . $row->id . ')">
            <i class="fas fa-trash-alt"></i>
        </a>';
                        }
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        // Render the view for the academic year list
        return view('content.issue_creation.list');
    }

    public function add()
    {
        
        return view('content.issue_creation.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_year' => 'nullable|string|max:20',
            'to_year' => 'nullable|string|max:20',
            'acc_year' => [
                'required',
                'string',
                Rule::unique('acc_years')->where(function ($query) {
                    // Ensure the 'is_delete' field is 0 (not deleted)
                    return $query->where('is_delete', 0);
                }),
            ],
            'status' => 'nullable|string|max:100',
            // 'description' => 'nullable|string|max:100',
        ], [
            'acc_year.unique' => 'Academic year already exists.',
        ]);

        $acc_year = acc_year::create([
            'unique_id' => uniqid(),
            'from_year' => $request->from_year,
            'to_year' => $request->to_year,
            'acc_year' => $request->acc_year,
            'remarks' => $request->remarks,
            'status' => $request->status,
            
        ]);

        return response()->json(['success' => 'Category added successfully!', 'acc_year' => $acc_year]);
    }

    // Show the form to edit an existing academic year
    public function edit($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        $acc_year = acc_year::findOrFail($id);
        return view('content.academic_year_creation.form', compact('acc_year'));
    }

    // Update an existing academic year
    public function update(Request $request, $id)
    {
        $acc_year = acc_year::findOrFail($id);

        $request->validate([
            'from_year' => 'nullable|string|max:20',
            'to_year' => 'nullable|string|max:20',
            'acc_year' => [
                'required',
                'string',
                Rule::unique('acc_years')->ignore($acc_year->id)->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'remarks' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:100',
        ]);

        $acc_year->update([
            'from_year' => $request->from_year,
            'to_year' => $request->to_year,
            'acc_year' => $request->acc_year,
            'remarks' => $request->remarks,
            'status' => $request->status,
            
        ]);

        return response()->json(['success' => 'Academic year updated successfully!', 'acc_year' => $acc_year]);
    }

    // Delete an existing academic year
    public function destroy($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        
        $acc_year = acc_year::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('financeYear')->with('success', 'Academic Year deleted successfully!');
    }

}