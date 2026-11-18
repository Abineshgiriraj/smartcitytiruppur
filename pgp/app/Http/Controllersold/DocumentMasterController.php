<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\DocumentMaster;

class DocumentMasterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $document_master = DB::table('document_master')
                ->join('person_type', 'document_master.category_type', '=', 'person_type.unique_id')
                ->select('document_master.id', 'person_type.person_type', 'type_of_proof')
                ->where('document_master.is_delete', 0)
                ->get();

            return DataTables::of($document_master)
                ->addIndexColumn()


                // In your controller's index method, update the addColumn for actions
                ->addColumn('action', function ($row) {
                    $editUrl = route('document_master_edit', $row->id);

                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</a>';
                    return $btn;

                })


                ->make(true);
        }
        return view('content.document_master.list');
    }

    public function add()
    {
        return view('content.document_master.form');
    }

    public function edit($id)
    {
        $documentMaster = DocumentMaster::findOrFail($id);
        return view('content.document_master.edit', compact('documentMaster'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'category_type' => 'required|string|max:255',
            'type_of_proof' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        // $exists = DocumentMaster::where('category_type', $request->category_type)
        //     ->exists();
        // if ($exists) {
        //     return response()->json(['error', 'Already exists'], 422);
        // }


        $documentMaster = DocumentMaster::create([
            'unique_id' => unique_id(),
            'category_type' => $request->category_type,
            'type_of_proof' => $request->type_of_proof,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'Category Proof added successfully!', 'Category Proof' => $documentMaster]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'category_type' => 'required|string|max:255',
            'type_of_proof' => 'nullable|string|max:1000',
        ]);

        $documentMaster = DocumentMaster::findOrFail($id);

        // // Check if the category_type already exists in other entries
        // $exists = DocumentMaster::where('category_type', $request->category_type)
        //     ->where('id', '!=', $id) // Exclude the current entry
        //     ->exists();

        // if ($exists) {
        //     return response()->json(['error' => 'Category Proof already exists.'], 422);
        // }

        // Update the category_type and other fields
        $documentMaster->category_type = $request->category_type;
        $documentMaster->type_of_proof = $request->type_of_proof;
        $documentMaster->is_active = $request->status; // Assuming you're using this field
        $documentMaster->save();

        return response()->json(['success' => 'Category Proof updated successfully!']);
    }


    public function destroy($id)
    {
        $acc_year = DocumentMaster::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('document_master')->with('success', 'Category Proof deleted successfully!');
    }

}
