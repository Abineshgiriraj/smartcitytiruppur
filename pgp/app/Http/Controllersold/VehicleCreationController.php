<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\Vehiclecreation;

use Illuminate\Support\Facades\DB;



class VehicleCreationController extends Controller
{
    public function index(Request $request)
    {


        if ($request->ajax()) {
            $vehicle_creation = DB::table('vehicle_creation')
                ->join('cargo_creation', 'vehicle_creation.cargo', '=', 'cargo_creation.unique_id')
                ->select('vehicle_creation.id', 'cargo_creation.cargo', 'vehicle_creation.vehicle_type')
                ->where('vehicle_creation.is_delete', '=', 0)
                ->get();

            return DataTables::of($vehicle_creation)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $edit_url = route("vehicle_creation.edit", $row->id);
                    $delete_url = route("vehicle_creation.delete", $row->id);
                    $csrfToken = csrf_token();


                    $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="delete-btn text-danger" data-id="' . $row->id . '" title="Delete" style="background:none; border:none; padding:10px;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</button>';
                    return $btn;
                })
                ->make(true);
        }
        return view('content.vehicle_creation.index');
    }

    public function create()
    {
        return view('content.vehicle_creation.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cargo' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',

        ]);

        $vehicle_creation = Vehiclecreation::create([


            'unique_id' => unique_id(),
            'cargo'     => $request->cargo,
            'vehicle_type'  => $request->vehicle_type,
            'status'        => $request->status

        ]);

        return response()->json(['vehicle created sucessfully', 'vehicle_creation' => $vehicle_creation]);
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'cargo' => 'required|string', // Example validation rule
            'vehicle_type' => 'required|string', // Example validation rule
            'status' => 'required|boolean', // Example validation rule
        ]);

        $vehicle_creation = Vehiclecreation::findorFail($id);

        $vehicle_creation->update([
            'unique_id' => unique_id(),
            'cargo' => $request->cargo,
            'vehicle_type' => $request->vehicle_type,
            'status'       => $request->status
        ]);

        return response()->json(['Updated Sucessfully', 'vehicle_creation' => $vehicle_creation]);
    }

    public function edit($id)
    {

        $vehicle_creation = Vehiclecreation::findorFail($id);
        return view('content.vehicle_creation.edit', compact('vehicle_creation'));
    }




    public function destroy($id)
    {
        $vehicle_creation = Vehiclecreation::find($id);
        if ($vehicle_creation) {
            $vehicle_creation->is_delete = 1; // Mark as deleted
            $vehicle_creation->save();

            return response()->json(['success' => true, 'message' => 'Category marked as deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    }
}