<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // ✅ important
use Yajra\DataTables\Facades\DataTables; 

class Locationcontroller extends Controller
{
    
     public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('location')
                ->join('zone', 'location.zone', '=', 'zone.unique_id')
                ->join('ward', 'location.ward', '=', 'ward.unique_id')
                ->select(
                    'zone.zone as zone_name',
                    'ward.ward as ward_name',
                    'location.location as location_name',
                    'location.description',
                    'location.is_active',
                    'location.unique_id'
                )
                ->where('location.is_delete', 0);

            // ✅ use the imported facade
            return DataTables::of($data)
                ->addIndexColumn() // adds DT_RowIndex
                ->addColumn('action', function ($row) {
                    $editUrl = route('location.edit', ['id' => $row->unique_id]);
                    $btn  = '<a href="'.$editUrl.'" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\''.$row->unique_id.'\')" style="background:none;border:none;padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.location.list');
    }


    public function add()
    {
        return view('content.location.form');
    }

    // CREATE
    public function store(Request $request)
    {
        $zone        = $request->input('zone');
        $wards       = (array) $request->input('ward', []); // multi-select
        $location    = $request->input('location');
        $description = $request->input('description', '');
        $is_active   = (int) $request->input('is_active', 1);

        foreach ($wards as $wardUniqueId) {
            DB::statement('CALL sp_manage_location(?, ?, ?, ?, ?, ?, ?, ?)', [
                'INSERT',                    // p_action
                null,                        // p_id
                (string) Str::uuid(),        // p_unique_id  ✅ now works
                $zone,                       // p_zone
                $wardUniqueId,               // p_ward
                $location,                   // p_location
                $description,                // p_description
                $is_active,                  // p_is_active
            ]);
        }

        return response()->json(['message' => 'Added successfully'], 200);
    }

     public function edit($id)
    {
        $location = DB::table('location')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$location) {
            return redirect()->back()->with('error', 'location not found.');
        }

        return view('content.location.form', compact('location'));
    }
    // UPDATE (single row)
    public function update(Request $request, string $unique_id)
    {
        $zone        = $request->input('zone');
        $ward        = $request->input('ward');
        if (is_array($ward)) { $ward = $ward[0] ?? null; }
        $location    = $request->input('location');
        $description = $request->input('description', '');
        $is_active   = (int) $request->input('is_active', 1);

        $row = DB::table('location')->where('unique_id', $unique_id)->where('is_delete', 0)->first();
        if (!$row) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        DB::statement('CALL sp_manage_location(?, ?, ?, ?, ?, ?, ?, ?)', [
            'UPDATE',
            $row->id,
            $unique_id,
            $zone,
            $ward,
            $location,
            $description,
            $is_active,
        ]);

        return response()->json(['message' => 'Updated successfully'], 200);
    }

    // SOFT DELETE
    public function destroy(string $unique_id)
    {
        $row = DB::table('location')->where('unique_id', $unique_id)->where('is_delete', 0)->first();
        if (!$row) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        DB::statement('CALL sp_manage_location(?, ?, ?, ?, ?, ?, ?, ?)', [
            'DELETE',
            $row->id,
            null,
            null,
            null,
            null,
            null,
            null,
        ]);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
