<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class grninwardController extends Controller {

    public function index( Request $request ) {
        return view( 'content.grn_inward.list' );
        // No need to pass $categorys, it's loaded via AJAX
}


    // Display the form to add a new center

    public function add() {
        return view( 'content.grn_inward.form1' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        // Validate the form data
        $request->validate( [
            'category_name' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
        ] );

        // Save the validated data to the database

        Category::create( [
            'unique_id' => unique_id(),
            'category_id' => category_id(),
            'category_name' => $request->category_name,
            'group_id' => $request->group_id,
            'description' => $request->description,
        ] );

        // Return a JSON response for AJAX
        return response()->json( [
            'success' => true,
            'message' => 'Category created successfully!',
        ] );
    }

    public function edit( $id ) {
        $Category_id = Category::where( 'unique_id', $id )
        ->where( 'is_delete', 0 )
        ->first();

        if ( !$Category_id ) {
            return redirect()->route( 'category' )->with( 'error', 'Category not found.' );
        }

        return view( 'content.category.form', compact( 'Category_id' ) );
    }

    public function update( Request $request, $id ) {

        $request->validate( [
            'category_name' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
            'description' => 'string|max:255',

        ] );

        $Category = Category::where( 'unique_id', $id );

        $Category->update( [
            'category_name' => $request->category_name,
            'group_id' => $request->group_id,
            'description' => $request->description,
        ] );

        return response()->json( [ 'Category Updated Sucessfully', 'Category' => $Category ] );
    }

    public function destroy( $id ) {
        $Category = Category::where( 'unique_id', $id )->first();
        // get actual model

        if ( !$Category ) {
            return response()->json( [
                'success' => false,
                'message' => 'Category not found.'
            ] );
        }

        // Soft delete by setting is_delete flag
        $Category->is_delete = 1;
        $Category->save();

        return response()->json( [
            'success' => true,
            'message' => 'Deleted successfully!'
        ] );
    }

}

function category_id() {
    // Get the latest non-deleted category
    $lastCategory = Category::where( 'is_delete', 0 )
    ->select( 'category_id' )
    ->orderByDesc( 'id' ) // Or orderByDesc( 'category_id' ) if numeric
    ->first();
    // Corrected regex pattern: no spaces!
    if ( $lastCategory && preg_match( '/CATG-( \d+ )/', $lastCategory->category_id, $matches ) ) {
        $nextId = ( int )$matches[ 1 ] + 1;
    } else {
        $nextId = 1;
    }

    // Format the next ID: CATG0001, CATG0002, etc.
    return 'CATG-' . str_pad( $nextId, 4, '0', STR_PAD_LEFT );
    }