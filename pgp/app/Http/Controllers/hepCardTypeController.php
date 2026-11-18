<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\HEPCardType;

class hepCardTypeController extends Controller
{
    public function index(Request $request)
    {

      if ($request->ajax()) {

        $hepcard_type = DB::table('hepcard_type')
            ->select('id', 'hepcard_type','is_active')
            ->where('is_delete', 0)
            ->get();

        return DataTables::of($hepcard_type)

            ->addIndexColumn()

            // In your controller's index method, update the addColumn for actions
            ->addColumn('action', function ($row) {
                $editUrl = route('hepCardTypeEdit', $row->id);

                $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                $btn .= '<i class="fas fa-trash-alt"></i>';
                $btn .= '</a>';
                return $btn;

            })

            ->make(true);
    }
        return view('content.hepCardType.list');
    }

    public function add()
    {
        return view('content.hepCardType.form');
    }



    public function store(Request $request)
    {

        $request->validate([
            'hepCard_type' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        $exists = HEPCardType::where('hepCard_type', $request->hepCard_type)
            ->exists();

        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $hepCardType = HEPCardType::create([
            'unique_id' => unique_id(),
            'hepcard_type' => $request->hepCard_type,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'HEP Card Type added successfully!', 'HEP Card Type' => $hepCardType]);
    }
    public function edit($id)
    {
        $hepCardType = HEPCardType::findOrFail($id);
        return view('content.hepCardType.edit', compact('hepCardType'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'hepCard_type' => 'required|string|max:255',
        ]);

        $HEPCardType = HEPCardType::findOrFail($id);

        // Check if the person_type already exists in other entries
        $exists = HEPCardType::where('hepCard_type', $request->hepCard_type)
            ->where('id', '!=', $id) // Exclude the current entry
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'HEP Card Type already exists.'], 422);
        }

        // Update the person_type and other fields
        $HEPCardType->hepCard_type = $request->hepCard_type;
        $HEPCardType->is_active = $request->status; // Assuming you're using this field
        $HEPCardType->save();

        return response()->json(['success' => 'HEP Card Type updated successfully!']);
    }


    public function destroy($id)
    {
        $HEPCardType = HEPCardType::findOrFail($id);
        $HEPCardType->is_delete = 1;
        $HEPCardType->save();
        return redirect()->route('hepCardType')->with('success', 'HEP Card Type deleted successfully!');
    }

}
