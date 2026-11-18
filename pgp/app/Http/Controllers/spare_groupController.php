<?php

namespace App\Http\Controllers;

use App\Models\sparegroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class spare_groupController extends Controller {

    public function index( Request $request ) {
        if ( $request->ajax() ) {
            $group = sparegroup::where( 'is_delete', 0 )->get();

            return DataTables::of( $group )
            ->addIndexColumn()
            ->addColumn(
                'action',

                function ( $row ) {
                    $editUrl = route( 'sparegroup.edit', $row->unique_id );
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                }
            )
            ->rawColumns( [ 'action' ] ) // Needed to render HTML
            ->make( true );
        }

        return view( 'content.spare_group.list' );
    }

    // Display the form to add a new center

    public function add() {
        return view( 'content.spare_group.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        // Validate the form data
        $request->validate( [
            'group_name' => 'required|string|max:255',
            'description' => 'nullable',
        ] );

        sparegroup::create( [
            'unique_id' => unique_id(),
            'group_id' => spare_id(),
            'group_name' => $request->group_name,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at,
        ] );

        // dd( $category );
        // Return a JSON response for AJAX
        return response()->json( [
            'success' => true,
            'message' => 'Group Name created successfully!',
        ] );
    }

    public function edit( $id ) {
        $group_creation = sparegroup::where( 'unique_id', $id )
        ->where( 'is_delete', 0 )
        ->first();

        if ( !$group_creation ) {
            return redirect()->route( 'group_creation' )->with( 'error', 'Group Name not found.' );
        }

        return view( 'content.spare_group.form', compact( 'group_creation' ) );
    }

    public function update( Request $request, $id ) {

        $request->validate( [
            'group_name' => 'required|string|max:255',
            'description' => 'required|string',
        ] );

        $group = sparegroup::where( 'unique_id', $id );
        $group->update( [
            'group_name' => $request->group_name,
            'description' => $request->description,
        ] );

        return response()->json( [ 'group Updated Sucessfully', 'group' => $group ] );
    }

    public function destroy( $id ) {
        $group = sparegroup::where( 'unique_id', $id )->first();

        if ( !$group ) {
            return response()->json( [ 'success' => false, 'message' => 'group not found.' ] );
        }

        // Soft delete by setting is_delete flag
        $group->is_delete = 1;
        $group->save();

        return response()->json( [ 'success' => true, 'message' => 'Deleted successfully!' ] );
    }

}
function spare_id() {
    // Get the latest non-deleted group
    $lastCategory = sparegroup::where('is_delete', 0)
        ->select('group_id')
        ->orderByDesc('id')
        ->first();

    if ($lastCategory && preg_match('/SPG-(\d+)/', $lastCategory->group_id, $matches)) {
        $nextId = (int) $matches[1] + 1;
    } else {
        $nextId = 1;
    }

    return 'SPG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}
