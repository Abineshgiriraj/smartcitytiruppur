<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortAreaAccess;
use App\Models\PortAreaSub;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;



class PortAreaAccessController extends Controller
{
    // Show list of port areas

    public function index(Request $request)
    {
        if ($request->ajax()) {


            $port_area_access  = DB::table('port_area_access')
                ->select('id', 'form_unique_id', 'unique_id', 'area_name', 'short_name', 'description', 'is_active')
                ->where('is_delete', '=', 0)
                ->where('is_active', '=', 1)
                ->get();


            


            return DataTables::of($port_area_access)
                ->addIndexColumn()
                ->addcolumn('action', function ($row) {


                    $csrfToken = csrf_token();

                    return '<a href="' . route('portAreaAccess.edit', $row->form_unique_id) . '" class="text-primary">
                <i class="fas fa-edit"></i>
            </a>
            <a class="text-danger ml-2" onclick="confirmDelete(' . $row->id . ')">
                <i class="fas fa-trash-alt"></i>
            </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }




        // $user_type = department::where('is_delete', 0)->get();
        return view('content.port_area_access.index');
        // return view('content.user_type.index');
    }

    // $port_area = PortAreaAccess::all();
    // return view( 'content.port_area_access.index', compact( 'port_area' ) );
    // }

    // Show form for adding new port area

    public function add()
    {
        return view('content.port_area_access.create', ['port_area' => null]);
    }

    // Store new port area

    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required',
            'status' => 'required|string',
            // 'description' =>  'nullable|string|max:250',
        ]);

        PortAreaAccess::create([
            'unique_id' => unique_id(),
            'form_unique_id' => $request->form_unique_id,
            'area_name' => $request->area_name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return redirect()->route('portAreaAccess')->with('success', 'Port Area added successfully');
    }

    // Show form for editing a port area

    public function edit($form_unique_id)
    {
        $port_area = PortAreaAccess::where('form_unique_id', $form_unique_id)
            ->where('is_delete', 0)
            ->first();

        // dd($port_area);

        $port_area_sub = PortAreaSub::where('form_unique_id', $form_unique_id)
            ->where('port_area_sub.is_delete', 0)
            ->join('access_gate', 'port_area_sub.gate_type', 'access_gate.unique_id')
            ->select('access_gate.access_gate as gate_type_name', 'port_area_sub.*')
            ->first();
        // dd($port_area_sub);
        // 

        return view('content.port_area_access.edit', compact('port_area', 'port_area_sub'));
    }

    public function subdetials(Request $request)
    {
        $port_area = DB::table('port_area_sub')->where('port_area_sub.form_unique_id', $request->main_unique_id)
            ->join('access_gate', 'port_area_sub.gate_type', 'access_gate.unique_id')
            // ->join('port_area_access', 'port_area_sub.form_unique_id', '=', 'port_area_access.form_unique_id')
            ->where('port_area_sub.is_delete', 0)
            ->select('access_gate.access_gate as gate_type_name', 'port_area_sub.*')
	    ->orderBy('id','asc')
            ->get();

        // dd($port_area);

        return response()->json(['listed successfully', 'port_area' => $port_area]);
    }

    // Update existing port area

    // public function update( Request $request, $id ) {
    //     $request->validate( [
    //         'area_name' => 'required',
    //         'short_name' => 'required',
    //         'description' => 'required',
    //         'status' => 'required|in:Active,In Active',
    // ] );

    //     $port_area = PortAreaAccess::findOrFail( $id );
    //     $port_area->update( [
    //         'area_name' => $request->area_name,
    //         'short_name' => $request->short_name,
    //         'description' => $request->description,
    //         'status' => $request->status,
    // ] );
    //     return redirect()->route( 'port_area_access' )->with( 'success', 'Port Area updated successfully' );
    // }
    public function update(Request $request)
    {
        $request->validate([
            'area_name' => 'required',
            'short_name' => 'required',
            'form_unique_id' =>  'required',
            'status' => 'required|string',

        ]);

        $port_area = PortAreaAccess::where('form_unique_id', $request->form_unique_id);
        // $port_area_sub  = PortAreaSub::where('unique_id',$request->sub_unique_id);


        $port_area->update([
            'form_unique_id' => $request->form_unique_id,
            'area_name' => $request->area_name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return response()->json(['Department Updated Sucessfully', 'port_area' => $port_area]);
    }


    public function substore(Request $request)
    {

        $request->validate([
            'gate_type' => 'required',
            'gate_no'  => 'required',
            'form_unique_id' => 'required',
            'access_area'   => 'required',
            'short_name'  => 'required'
        ]);




        if ($request->sub_unique_id) {
            $sublist = PortAreaSub::where('unique_id', $request->sub_unique_id)->first();
            // dd($sublist);

            $sublist->update([
                'unique_id'         => $request->sub_unique_id,
                'form_unique_id'    => $request->form_unique_id,
                'access_area'       => $request->access_area,
                'gate_type'         => $request->gate_type,
                'gate_no'           => $request->gate_no,
                'short_name'        => $request->short_name
            ]);
        } else {

            $sublist = PortAreaSub::create([
                'unique_id'     => unique_id(),
                'form_unique_id'    => $request->form_unique_id,
                'access_area'       => $request->access_area,
                'gate_type'         => $request->gate_type,
                'gate_no'           => $request->gate_no,
                'short_name'        => $request->short_name

            ]);
        }


        return response()->json(['sublist created Successfully', 'sublist' => $sublist]);
    }

    public function subupdate(Request $request)
    {

        $request->validate([
            'gate_type' => 'required',
            'gate_no'  => 'required',
            'form_unique_id' => 'required',
            'access_area'   => 'required'
        ]);


        if ($request->sub_unique_id) {

            $sublist = DB::table('port_area_sub')->where('port_area_sub.unique_id', $request->sub_unique_id)->first();
            // dd($sublist);

            $sublist->update([
                'unique_id'         => $request->sub_unique_id,
                'form_unique_id'    => $request->form_unique_id,
                'access_area'       => $request->access_area,
                'gate_type'         => $request->gate_type,
                'short_name'        => $request->short_name,
                'gate_no'           => $request->gate_no
            ]);
        } else {

            $sublist =  DB::table('port_area_sub')->where('form_unique_id', $request->form_unique_id)->first();
            $sublist->create([
                'unique_id'         => unique_id(),
                'form_unique_id'    => $request->form_unique_id,
                'access_area'       => $request->access_area,
                'gate_type'         => $request->gate_type,
                'gate_no'           => $request->gate_no,
                'short_name'        => $request->short_name,

            ]);
        }




        return response()->json(['sublist created Successfully', 'sublist' => $sublist]);
    }




    public function subdelete($unique_id)
    {
        $sublist = PortAreaSub::where('unique_id', $unique_id)->firstOrFail();

        $sublist->is_delete = 1;
        $sublist->save();

        return response()->json(['message' => 'Deleted successfully']);
    }

    // Delete a port area

    // public function destroy( $id ) {
    //     $port_area = PortAreaAccess::findOrFail( $id );
    //     $port_area->delete();
    //     return redirect()->route( 'port_area_access' )->with( 'success', 'Port Area deleted successfully' );
    // }

    public function destroy($id)
    {
        // Find the company by ID
        $port_area = PortAreaAccess::find($id);

        // Check if the port_area exists
        if (!$port_area) {
            return redirect('/portAreaAccess')->with('error', 'port_area not found.');
        }

        // Soft delete by setting is_delete flag
        $port_area->is_delete = 1;
        $port_area->save();

        // Redirect back to the list page with a success message
        return redirect('/portAreaAccess')->with('success', 'Deleted Successfully');
    }
}
