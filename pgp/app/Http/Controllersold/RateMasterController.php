<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;



use App\Models\Ratemaster;
use Yajra\DataTables\DataTables;


use Illuminate\Support\Facades\DB;


class RateMasterController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {



            $rate_master_approval_query = DB::table('rate_master')
                ->select('allowed_type')
                ->where('is_delete', 0)
                ->distinct()
                ->get();



            $results = [];

            foreach ($rate_master_approval_query as $type) {
                $allowed_type = $type->allowed_type;

                $rate_master = DB::table('rate_master')
                    ->Join('access_gate', 'rate_master.access_gate', '=', 'access_gate.unique_id')
                    ->where('access_gate.is_delete', 0)
                    ->where('rate_master.is_delete', 0);

                if ($allowed_type === 'Vehicle') {
                    $rate_master->join('cargo_creation', 'rate_master.vehicle_person_type', 'cargo_creation.unique_id')
                        ->select(
                            'rate_master.id',
                            'access_gate.access_gate',
                            'rate_master.allowed_type',
                            'cargo_creation.cargo as vehicle_person',
                            'rate_master.validity',
                            'rate_master.rate',
                        );

                    // dd($rate_master);
                }

                if ($allowed_type === 'Person') {
                    $rate_master->join('person_type', 'rate_master.vehicle_person_type', 'person_type.unique_id')
                        ->select(
                            'rate_master.id',
                            'access_gate.access_gate',
                            'rate_master.allowed_type',
                            'person_type.person_type as vehicle_person',
                            'rate_master.validity',
                            'rate_master.rate',
                        );
                }

                $rate_master_final = $rate_master->get();

                $results = array_merge($results, $rate_master_final->toArray());
            }


            //     ->leftJoin('vehicle_creation', 'rate_master.vehicle_type', '=', 'vehicle_creation.unique_id')
            //     ->leftJoin('person_type', 'rate_master.person_type', '=', 'person_type.unique_id')
            //     ->select(
            //         'rate_master.id',
            //         'access_gate.access_gate',
            //         'rate_master.allowed_type',
            //         'vehicle_creation.vehicle_type',
            //         'person_type.person_type',
            //         'rate_master.validity',
            //         'rate_master.rate',
            //         'rate_master.approval',
            //         DB::raw("CASE 
            //     WHEN rate_master.is_active = 1 THEN 'Active' 
            //     ELSE 'Inactive' 
            // END AS status")
            //     )
            //     ->where('rate_master.is_delete', 0)
            //     ->where(function ($query) {
            //         $query->where('vehicle_creation.unique_id', '!=', null)
            //             ->where('person_type.unique_id', '=', null)
            //             ->orWhere(function ($query) {
            //                 $query->where('person_type.unique_id', '!=', null)
            //                     ->where('vehicle_creation.unique_id', '=', null);
            //             });
            //     })




            // dd($rate_master);


            return DataTables::of($results)
                ->addIndexColumn()
                ->addcolumn('action', function ($row) {

                    $edit_url = route('rate_master.edit', $row->id);
                    $delete_url = route('rate_master.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="delete-btn text-danger" data-id="' . $row->id . '" title="Delete" style="background:none; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</button>';
                    return $btn;


                    // $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<form action="' . $delete_url . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete?\');">';
                    // $btn .=  '<input type="hidden" name="_token" value="' . csrf_token() . '">'; // Add CSRF token
                    // $btn .=  '<input type="hidden" name="_method" value="DELETE">'; // Specify the method
                    // $btn .= '<button type="submit" class="text-danger" title="Delete" style="background:none; border:none; padding:0;">';
                    // $btn .= '<i class="fas fa-trash-alt"></i>';
                    // $btn .= '</button>';
                    // $btn .= '</form>';
                    // return $btn;
                })
                ->make(true);
        }
        return view('content.rate_master.index');
    }
    public function add()
    {
        return view('content.rate_master.create');
    }
    public function store(Request $request)
    {

        $request->validate([
            'access_gate' => 'required|string|max:255',
            'allowed_type' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'person_type' => 'nullable|string|max:255',
            'validity' => 'required|string|max:255',
            'rate' => 'required|string|max:255',
            'penalty_per_hour' => 'required|string|max:255',
            'penalty_per_day' => 'required|string|max:255',
            'approval' => 'required|string|max:255',
        ]);


        if ($request->allowed_type == 'Vehicle') {


            $rate_master = Ratemaster::create([
                'unique_id' => unique_id(),
                'access_gate' => $request->access_gate,
                'allowed_type' => $request->allowed_type,
                'vehicle_person_type' => $request->vehicle_type,
                'validity' => $request->validity,
                'rate' => $request->rate,
                'penalty_per_hour' => $request->penalty_per_hour,
                'penalty_per_day' => $request->penalty_per_day,
                'approval' => $request->approval,
                'entry_date' => Carbon::now()->toDateString(),


            ]);
            return response()->json(['Rate master Created Successfully', 'rate_master' => $rate_master]);
        } elseif ($request->allowed_type == 'Person') {
            $rate_master = Ratemaster::create([
                'unique_id' => unique_id(),
                'access_gate' => $request->access_gate,
                'allowed_type' => $request->allowed_type,
                'vehicle_person_type' => $request->person_type,
                'validity' => $request->validity,
                'rate' => $request->rate,
                'penalty_per_hour' => $request->penalty_per_hour,
                'penalty_per_day' => $request->penalty_per_day,
                'approval' => $request->approval,
                'entry_date' => Carbon::now()->toDateString(),

            ]);
            return response()->json(['Rate master Created Successfully', 'rate_master' => $rate_master]);
        }
    }

    public function edit($id)
    {

        $rate_master = Ratemaster::findorFail($id);
        // dd($rate_master);
        return view('content.rate_master.edit', compact('rate_master'));
    }





    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'access_gate' => 'required',
            'allowed_type' => 'required',
            'vehicle_type' => 'required_if:allowed_type,Vehicle',
            'person_type' => 'required_if:allowed_type,Person',
            'validity' => 'required',
            'rate' => 'required|numeric',
            'approval' => 'required',
            'status' => 'required|in:1,2', // Assuming 1 = Active, 2 = Inactive
        ]);

        try {
            // Find the RateMaster by ID

            // Update the model with validated data
            // $rateMaster->update($request->all());
            if ($request->allowed_type == 'Vehicle') {

                $rate_master = Ratemaster::findOrFail($id);

                $rate_master->update([
                    'unique_id' => unique_id(),
                    'access_gate' => $request->access_gate,
                    'allowed_type' => $request->allowed_type,
                    'vehicle_person_type' => $request->vehicle_type,
                    'validity' => $request->validity,
                    'rate' => $request->rate,
                    'penalty_per_hour' => $request->penalty_per_hour,
                    'penalty_per_day' => $request->penalty_per_day,
                    'approval' => $request->approval,
                    'entry_date' => Carbon::now()->toDateString(),


                ]);
                return redirect()->route('rate_master')->with('success', 'Rate master updated successfully.');
            } elseif ($request->allowed_type == 'Person') {

                $rate_master = Ratemaster::findOrFail($id);

                $rate_master->update([
                    'unique_id' => unique_id(),
                    'access_gate' => $request->access_gate,
                    'allowed_type' => $request->allowed_type,
                    'vehicle_person_type' => $request->person_type,
                    'validity' => $request->validity,
                    'rate' => $request->rate,
                    'penalty_per_hour' => $request->penalty_per_hour,
                    'penalty_per_day' => $request->penalty_per_day,
                    'approval' => $request->approval,
                    'entry_date' => Carbon::now()->toDateString(),
                ]);
                return redirect()->route('rate_master')->with('success', 'Rate master updated successfully.');
            }

            // dd($rateMaster);

        } catch (ModelNotFoundException $e) {
            return redirect()->route('rate_master')->with('error', 'Rate master not found.');
        } catch (\Exception $e) {
            return redirect()->route('rate_master')->with('error', 'An error occurred while updating.');
        }
    }

    // return response()->json(['updated successfully', 'rate_master' => $rate_master]);


    public function destroy($id)
    {
        $rate_master = Ratemaster::find($id);
        if ($rate_master) {
            $rate_master->is_delete = 1; // Mark as deleted
            $rate_master->save();

            return response()->json(['success' => true, 'message' => 'Category marked as deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    }
}
