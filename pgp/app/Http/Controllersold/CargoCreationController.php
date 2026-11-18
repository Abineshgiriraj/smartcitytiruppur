<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\CargoCreation;

class CargoCreationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $cargo_creation = DB::table('cargo_creation')
                ->select('id', 'cargo')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($cargo_creation)

                ->addIndexColumn()

                // In your controller's index method, update the addColumn for actions
                ->addColumn('action', function ($row) {
                    $editUrl = route('cargo_creation_edit', $row->id);

                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</a>';
                    return $btn;

                })

                ->make(true);
        }
        return view('content.cargo_creation.list');
    }

    public function add()
    {
        return view('content.cargo_creation.form');
    }

    public function edit($id)
    {
        $cargoCreation = CargoCreation::findOrFail($id);
        return view('content.cargo_creation.edit', compact('cargoCreation'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'cargo' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);


        $exists = CargoCreation::where('cargo', $request->cargo)
            ->exists();
        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $cargoCreation = CargoCreation::create([
            'unique_id' => unique_id(),
            'cargo' => $request->cargo,
            'is_active' => $request->status
        ]);
        return response()->json(['success' => 'Cargo added successfully!', 'Cargo' => $cargoCreation]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'cargo' => 'required|string|max:255',
        ]);

        $cargoCreation = CargoCreation::findOrFail($id);

        // Check if the cargo already exists in other entries
        $exists = CargoCreation::where('cargo', $request->cargo)
            ->where('id', '!=', $id) // Exclude the current entry
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Cargo already exists.'], 422);
        }

        // Update the cargo and other fields
        $cargoCreation->cargo = $request->cargo;
        $cargoCreation->is_active = $request->status; // Assuming you're using this field
        $cargoCreation->save();

        return response()->json(['success' => 'Cargo updated successfully!']);
    }


    public function destroy($id)
    {
        $acc_year = CargoCreation::findOrFail($id);
        $acc_year->is_delete = 1;
        $acc_year->save();
        return redirect()->route('cargo_creation')->with('success', 'Cargo deleted successfully!');
    }

}
