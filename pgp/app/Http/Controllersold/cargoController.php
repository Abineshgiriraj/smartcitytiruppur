<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cargoEiRegistration;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;



class cargoController extends Controller
{
    public function index()
    {
        return view('content.new_cargo.index');
    }

    public function create()
    {
        return view('content.new_cargo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required|integer',
            'exp_imp_no' => 'required|string|max:255',
            'exp_imp_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'cargo_name' => 'required|string|max:255',
            'agent_type' => 'required|string|max:255',
            'agent_details' => 'required|string|max:255',
            'vessel_name' => 'required|string|max:255',
            'package_qty' => 'required|integer',
            'org_destination' => 'required|string|max:255',
            'vessel_sr_no' => 'required|string|max:255',
        ]);

        cargoEiRegistration::create([
            'unique_id' => unique_id(),
            'entry_date' => Carbon::now()->toDateString(),
            'transaction_type' => $request->transaction_type,
            'exp_imp_no' => $request->exp_imp_no,
            'exp_imp_name' => $request->exp_imp_name,
            'address' => $request->address,
            'cargo_name' => $request->cargo_name,
            'agent_type' => $request->agent_type,
            'agent_details' => $request->agent_details,
            'package_qty' => $request->package_qty,
            'vessel_name' => $request->vessel_name,
            'vessel_sr_no' => $request->vessel_sr_no,
            'total_weight' => $request->total_weight,
            'org_destination' => $request->org_destination,
        ]);

        return response()->json(['status' => 'success']);
    }
    public function getdata()
    {
        $cargo_ei = cargoEiRegistration::where('is_delete', 0)->get();
        return DataTables::of($cargo_ei)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('action', function ($cargo_ei) {
                return
                    '<a href="' . route('cargo_ei.edit', $cargo_ei->unique_id) . '" class="text-primary"> <i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' . $cargo_ei->unique_id . '"><i class="fas fa-trash-alt"></i></a>';
            })
            ->make(true);
    }
    public function edit($unique_id)
    {
        $cargo_ei = cargoEiRegistration::where('unique_id', $unique_id)->first();

        return view('content.new_cargo.create', compact('cargo_ei'));
    }

    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'usertype' => 'required|string|max:255',
        //     'status' => 'required|boolean',
        // ]);

        $cargo_ei = cargoEiRegistration::findOrFail($id);
        $cargo_ei->update([
            'entry_date' => Carbon::now()->toDateString(),
            'transaction_type' => $request->transaction_type,
            'exp_imp_no' => $request->exp_imp_no,
            'exp_imp_name' => $request->exp_imp_name,
            'address' => $request->address,
            'cargo_name' => $request->cargo_name,
            'agent_type' => $request->agent_type,
            'agent_details' => $request->agent_details,
            'package_qty' => $request->package_qty,
            'vessel_name' => $request->vessel_name,
            'vessel_sr_no' => $request->vessel_sr_no,
            'total_weight' => $request->total_weight,
            'org_destination' => $request->org_destination,
        ]);

        return response()->json(['status' => 'User updated successfully']);
    }

    public function destroy($unique_id)
    {
        $cargo_ei = cargoEiRegistration::findOrFail($unique_id);
        $cargo_ei->update(['is_delete' => 1]);
        return response()->json(['status' => 'User Type Deleted Successfully']);
    }

}
