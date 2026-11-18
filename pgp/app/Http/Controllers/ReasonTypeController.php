<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\ReasonType;

class ReasonTypeController extends Controller
{
    public function index(Request $request)
    {
      if ($request->ajax()) {

        $reason_type = DB::table('reason_type')
            ->select('id', 'reason_type','remarks')
            ->where('is_delete', 0)
            ->get();

        return DataTables::of($reason_type)

            ->addIndexColumn()

            // In your controller's index method, update the addColumn for actions
            ->addColumn('action', function ($row) {
                $editUrl = route('reasonTypeEdit', $row->id);

                $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                $btn .= '<i class="fas fa-trash-alt"></i>';
                $btn .= '</a>';
                return $btn;

            })

            ->make(true);
    }
        return view('content.reason_type.list');
    }

    public function add()
    {
        return view('content.reason_type.form');
    }



    public function store(Request $request)
    {

        $request->validate([
            'reason_type' => 'required|string|max:255',
            'remarks' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        $exists = ReasonType::where('reason_type', $request->reason_type)
            ->exists();

        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $ReasonType = ReasonType::create([
            'unique_id' => unique_id(),
            'reason_type' => $request->reason_type,
            'remarks' => $request->remarks,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'Reason Type added successfully!', 'Reason Type' => $ReasonType]);
    }
    public function edit($id)
    {
        $ReasonType = ReasonType::findOrFail($id);
        return view('content.reason_type.edit', compact('ReasonType'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'reason_type' => 'required|string|max:255',
        ]);

        $ReasonType = ReasonType::findOrFail($id);

        // Check if the person_type already exists in other entries
        $exists = ReasonType::where('reason_type', $request->person_type)
            ->where('id', '!=', $id) // Exclude the current entry
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Reason Type already exists.'], 422);
        }

        // Update the person_type and other fields
        $ReasonType->reason_type = $request->reason_type;
        $ReasonType->remarks = $request->remarks;
        $ReasonType->is_active = $request->status; // Assuming you're using this field
        $ReasonType->save();

        return response()->json(['success' => 'Reason Type updated successfully!']);
    }


    public function destroy($id)
    {
        $acc_year = ReasonType::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('reasonType')->with('success', 'Reason Type deleted successfully!');
    }

}
