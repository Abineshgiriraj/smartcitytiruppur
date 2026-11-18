<?php

namespace App\Http\Controllers;

use App\Models\StandardCreation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class standard_creation extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $standard = StandardCreation::all();
            return DataTables::of($standard)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('standard_creation.edit', $row->id);
                    $deleteUrl = route('standard_creation.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm">Delete</button>';
                    $btn .= '</form>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.standard_creation.list');
    }

    public function add()
    {
        return view('content.standard_creation.form');
    }
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:355',
            'school_taluk' => 'required|string|max:255',
            'school_district' => 'required|string|max:255',
            'school_zone' => 'required|string|max:255',
            'medium' => 'required|string|max:255',
            'standard' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'created_at'       => 'nullable|date',
            'updated_at'       => 'nullable|date',


        ]);

        $standard = StandardCreation::create([

            'unique_id' => unique_id(),
            'school_id' => $request->school_id,
            'school_name' => $request->school_name,
            'school_address' => $request->school_address,
            'school_taluk'  => $request->school_taluk,
            'school_district' => $request->school_district,
            'school_zone'   => $request->school_zone,
            'medium'    => $request->medium,
            'standard'  => $request->standard,
            'remarks'  =>  $request->remarks,
            'created_at'       => $request->created_at,
            'updated_at'       => now(),
        ]);

        return response()->json(['success' => 'standard added successfully', 'standard' => $standard]);
    }
    public function edit($id)
    {
        $standard = StandardCreation::findOrFail($id);
        return view('content.standard_creation.edit', compact('standard'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'required|string|max:255',
            'school_name' => 'required|string|max:300',
            'school_address' => 'required|string|max:300|',
            'school_taluk'  => 'required|string|max:200',
            'school_district' => 'required|string|max:255',
            'school_zone' => 'required|string|max:255',
            'medium' => 'required|string|max:255',
            'standard' => 'required|string|max:200',
            'remarks' => 'nullable|string|max:255',
            'created_at'       => 'nullable|date',
            'updated_at'       => 'nullable|date',
        ]);
        
            // $exists = StandardCreation::where('id', '!=', $id)
            //                             ->where(function($query) use ($request) {
            //                                 $query->where('school_id', $request->input('school_id'))
            //                                     ->orWhere('school_name', $request->input('school_name'))
            //                                     ->orWhere('school_address', $request->input('school_address'));
                                            
            //                             })  
            //                             ->exists();
                
            //     if ($exists) {
            //         return response()->json(['error' => 'Already exists.'], 422);
            //     }
    
        $standard = StandardCreation::findOrFail($id);

        $standard->update([

            'school_id' => $request->school_id,
            'school_name' => $request->school_name,
            'school_address' => $request->school_address,
            'school_taluk'  => $request->school_taluk,
            'school_district' => $request->school_district,
            'school_zone'   => $request->school_zone,
            'medium' => $request->medium,
            'standard' => $request->standard,
            'remarks'  => $request->remarks,
            'created_at'       => $request->created_at,
            'updated_at'       => now(),
        ]);

        return response()->json(['successs' => 'updated successfully', 'standard' => $standard]);
    }
    public function destroy($id)
    { {
            $biometric = StandardCreation::find($id);
            $biometric->is_delete = 1;
            $biometric->save();

            return redirect()->route('standard_list')->with('Success', 'Deleted Successfully');
        }
    }
}