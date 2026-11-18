<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\PersonType;

class PersonTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $person_type = DB::table('person_type')
                ->select('id', 'person_type')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($person_type)

                ->addIndexColumn()

                // In your controller's index method, update the addColumn for actions
                ->addColumn('action', function ($row) {
                    $editUrl = route('personTypeEdit', $row->id);
    
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</a>';
                    return $btn;

                })

                ->make(true);
        }
        return view('content.person_type.list');
    }

    public function add()
    {
        return view('content.person_type.form');
    }

    public function edit($id)
    {
        $PersonType = PersonType::findOrFail($id);
        return view('content.person_type.edit', compact('PersonType'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'person_type' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        $exists = PersonType::where('person_type', $request->person_type)
            ->exists();
        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $PersonType = PersonType::create([
            'unique_id' => unique_id(),
            'person_type' => $request->person_type,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'Person Type added successfully!', 'Person Type' => $PersonType]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'person_type' => 'required|string|max:255',
        ]);

        $PersonType = PersonType::findOrFail($id);

        // Check if the person_type already exists in other entries
        $exists = PersonType::where('person_type', $request->person_type)
            ->where('id', '!=', $id) // Exclude the current entry
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Person Type already exists.'], 422);
        }

        // Update the person_type and other fields
        $PersonType->person_type = $request->person_type;
        $PersonType->is_active = $request->status; // Assuming you're using this field
        $PersonType->save();

        return response()->json(['success' => 'Person Type updated successfully!']);
    }


    public function destroy($id)
    {
        $acc_year = PersonType::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('personType')->with('success', 'Person Type deleted successfully!');
    }

}