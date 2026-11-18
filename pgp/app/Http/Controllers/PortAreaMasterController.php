<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;


use App\Models\Portarea_sublist;
use App\Models\portareamaster;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PortAreaMasterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $portarea = DB::table('port_area_master')
            ->join('area_gate_master', 'port_area_master.gate_name', '=', 'area_gate_master.unique_id')
            ->join('port_area_access', 'port_area_master.port_area', '=', 'port_area_access.unique_id')
            ->where('port_area_master.is_delete', 0)
            ->select('port_area_master.*', 'area_gate_master.gate_name', 'port_area_access.area_name') // Select required columns
                ->orderBy('port_area_master.id', 'desc');
            $portarea = $portarea->get();

            // dd($portarea);

            return DataTables::of($portarea)
                ->addIndexColumn()
                ->addColumn(
                    'action',
                    function ($row) {

                        $edit_url = route('PortArea.edit', $row->form_unique_id);

                        $btn =  '<a href ="' .  $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                        $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')" style="background:none; border:none; padding:0;">';
                        $btn .= '<i class="fas fa-trash-alt"></i>';
                        $btn .= '</a>';

                        return $btn;
                    }
                )

                ->make(true);
        }
        return view('content.PortArea.index');
    }

    public function add()
    {
        return view('content.PortArea.create');
    }

    public function getdata()
    {
       // return view('content.PortArea.data');
    }

    //main list store
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            
            'port_area' => 'required|string|max:255',
            'gate_name'       => 'required|string|max:300',
          
        ]);

            $portarea =  portareamaster::create([
            'unique_id' => unique_id(),
            'form_unique_id' => $request->screen_unique_id,
                       'port_area' => $request->port_area,
                        'gate_name' => $request->gate_name,
                        'gate_shortName' => $request->gate_shortName
                      // Save the path of the uploaded photo
        ]);

        return response()->json(['message' => 'Port registered successfully', 'portarea' => $portarea, 'unique_id' => $portarea->unique_id]);
    }



    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'port_area' => 'required|string|max:255',
            'gate_name' => 'required|string|max:300',
        ]);
    
        // Find the record by unique_id
        $portarea = portareamaster::where('unique_id', $id)->firstOrFail();
    
        // Prepare the update data
        $portarea_update = [
            'port_area' => $request->port_area,
            'gate_name' => $request->gate_name,
        ];
    
        // Update the record
        $portarea->update($portarea_update);
    
        // Return a JSON response
        return response()->json([
            'message'   => 'Port updated successfully',
            'unique_id' => $portarea->unique_id,
        ]);
    }
    


    public function edit($form_unique_id)
    {
        // Fetch the main record using form_unique_id
        $portarea = portareamaster::where('form_unique_id', $form_unique_id)
            ->where('is_delete', 0)
            ->first();

        // Check if the main record exists
        if (!$portarea) {
            return redirect()->back()->with('error', 'Main record not found.');
        }

        // Fetch the sublist data using the form_unique_id from the main record
        $portareasublist = DB::table('port_area_master_sub')->where('port_area_master_sub.is_delete', 0)
            ->where('port_area_master_sub.form_unique_id', $portarea->form_unique_id)

                ->get();

        // Check if the sublist record exists and is not empty
        $portareasublist_first = $portareasublist->first();

        return view('content.PortArea.edit', compact('portarea', 'portareasublist', 'portareasublist_first'));
    }

    public function destroy($id)
    {

        $portarea  = portareamaster::findOrFail($id);

        $portarea->is_delete = 1;
        $portarea->save();

        return redirect()->route('PortArea')->with('success', 'portarea Data deleted successfully!');
    }

// main list end =================================================================

// sublist start===============


public function sublist(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'screen_unique_id' => 'required|string|max:255',
        'sublists' => 'required|string',  // Ensure 'sublists' is a string
    ]);

    // Decode the JSON string into an array
    $sublists = json_decode($request->sublists, true);

    if ($sublists && is_array($sublists)) {
        $screenUniqueId = $request->screen_unique_id;


        foreach ($sublists as $sublist) {
            $data = [
                'form_unique_id' => $screenUniqueId,
                'gate_shortname' => $sublist['gate_shortName'],
                'gate_type' => $sublist['gate_type'],
                'gate_no' => $sublist['gate_no'] ?? null,
                'gate_id' => $sublist['gate_shortName'].$sublist['gate_type'].'-'.$sublist['gate_no'],
                'rfid_reader_ip' => $sublist['rfid_reader_ip'] ?? null,
                'rfid_reader_serial_no' => $sublist['rfid_reader_serial_no'] ?? null,
                'boom_barrier_ip' => $sublist['boom_barrier_ip'] ?? null,
                'boom_barrier_serial_no' => $sublist['boom_barrier_serial_no'] ?? null,
            ];

            if (!empty($sublist['unique_id'])) {
                $existingSublist = Portarea_sublist::where('unique_id', $sublist['unique_id'])
                    ->where('form_unique_id', $screenUniqueId)
                    ->where('is_delete', 0)
                    ->first();

                if ($existingSublist) {
                    $existingSublist->update($data);
                }else {
                    Portarea_sublist::create(array_merge($data, [
                        'unique_id' => $sublist['unique_id']
                    ]));
                }
            } else {
                // Create a new record
                Portarea_sublist::create(array_merge($data, [
                    'unique_id' => unique_id(), // Generate a unique ID for new records
                ]));
            }
        }

        return response()->json(['message' => 'Sublists processed successfully.']);
    } else {
        return response()->json(['error' => 'No sublist data provided or invalid format.'], 400);
    }
}

// sublist table data
public function sublistindex1(Request $request)
    {
        // dd( 'hi' );
        // Retrieve the sublist data;
        $sublist = DB::table('port_area_master_sub')
            ->where('port_area_master_sub.form_unique_id', $request->screen_unique_id)           
            ->where('port_area_master_sub.is_delete', 0)
            ->get();

        // dd($sublist);

        // Return the data as a JSON response
        return response()->json($sublist);
    }

// sublist delete


public function delete($unique_id)
{
    // Find the entry by unique_id
    $Portareasublist = Portarea_sublist::where('unique_id', $unique_id)->first();
// dd($Portareasublist);
    // Check if the entry exists
    if (!$Portareasublist) {
        return response()->json(['message' => 'Entry not found'], 404);
    }

    // Delete the entry
    $Portareasublist->is_delete = 1;

    if ($Portareasublist->save()) {
        return response()->json(['message' => 'Sublist item deleted successfully.']);
    } else {
        return response()->json(['message' => 'Failed to delete sublist item.'], 500);
    }
}

public function sublist_update(Request $request, $id)
{
    // dd($id);
    // Validate the incoming request data
    $request->validate([
        'gate_type' => 'required|string',
        'gate_no' => 'required|string',
        'rfid_reader_ip' => 'required|string',
        'rfid_reader_serial_no' => 'required|string',
        'boom_barrier_ip' => 'nullable|string',
        'boom_barrier_serial_no' => 'nullable|string',
    ]);

    // Find the record by ID and form_unique_id to ensure you're updating the correct sublist
    $portarea_sublist = Portarea_sublist::where('unique_id', $id)
        ->where('form_unique_id', $request->screen_unique_id)
        ->firstOrFail();

    // Update the record
    $portarea_sublist->update([
        'gate_type' => $request->gate_type,
        'gate_shortname' => $request->gate_shortName,
        'gate_no' => $request->gate_no,
        'rfid_reader_ip' => $request->rfid_reader_ip,
        'rfid_reader_serial_no' => $request->rfid_reader_serial_no,
        'boom_barrier_ip' => $request->boom_barrier_ip,
        'boom_barrier_serial_no' => $request->boom_barrier_serial_no,
    ]);

    // Return a JSON response indicating success
    return response()->json([
        'success' => true,
        'message' => 'Sublist updated successfully',
        'portarea_sublist' => $portarea_sublist, // Optionally send the updated data back
    ]);
}


public function getGateDatas(Request $request){

    $Portareasublist = DB::table('area_gate_master')->where('form_unique_id', $request->portArea)->get();

    return response()->json($Portareasublist);

}

public function geteDataval(Request $request){

    $Portareasublist = DB::table('port_area_master')->where('gate_name', $request->gateId)->first('form_unique_id');

    $gateNo = DB::table('port_area_master_sub')->where('form_unique_id', $Portareasublist->form_unique_id)->get();

    return response()->json($gateNo);

}

public function gateValue(Request $request){

    $gateData = DB::table('area_gate_master')->where('unique_id', $request->gateId)->first('short_name');

    return response()->json($gateData);

}


// end sublist


}
