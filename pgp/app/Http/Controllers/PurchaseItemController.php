<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class PurchaseItemController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchase_item = DB::table('purchase_item as pi')
                ->leftJoin('group_creation as gc', 'pi.product_group_id', '=', 'gc.unique_id') // adjust column names if needed
                ->leftJoin('category as c', 'pi.product_category_id', '=', 'c.unique_id') // optional
                ->select([
                    'pi.*',
                    'gc.group_name as group_id',
                    'c.category_name', 
                ])
                ->where('pi.is_delete', 0)
                ->orderByDesc('pi.id')
                ->get();
            // dd($purchase_item);
            return DataTables::of($purchase_item)
                ->addIndexColumn()
                ->addColumn('status', fn($row) => $row->is_active ? 'Active' : 'Inactive')
                ->addColumn('action', function ($row) {
                    $editUrl = route('purchase_item.edit', $row->unique_id);
                    return '
                    <a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                    &nbsp;
                    <a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.purchase_item.list');
    }


    // Display the form to add a new center

    public function add()
    {
        return view('content.purchase_item.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'item_name' => 'required|string|max:255',
            'hsn_code' => 'required|string',
        ]);

        // Save the validated data to the database
        PurchaseItem::create([
            'unique_id' => unique_id(),
            'item_name' => $request->item_name,
            'hsn_code' => $request->hsn_code,
            'product_group_id' => $request->product_group,
            'product_category_id' => $request->product_category,
            'description' => $request->description,
            'is_active' => $request->status

        ]);

        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Item created successfully!',
        ]);
    }

    public function edit($id)
    {
        $purchase_item = PurchaseItem::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        if (!$purchase_item) {
            return redirect()->route('purchase_item')->with('error', 'Item not found.');
        }

        return view('content.purchase_item.form', compact('purchase_item'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'item_name' => 'required|string|max:255',
            'hsn_code' => 'required|string|max:15',

        ]);

        $purchase_item = PurchaseItem::where('unique_id', $id);

        $purchase_item->update([
            'item_name' => $request->item_name,
            'hsn_code' => $request->hsn_code,
            'product_group_id' => $request->product_group,
            'product_category_id' => $request->product_category,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return response()->json(['Item Updated Sucessfully', 'productItem' => $purchase_item]);
    }

    public function destroy($id)
    {
        $purchase_item = PurchaseItem::where('unique_id', $id)->first();
        // get actual model

        if (!$purchase_item) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $purchase_item->is_delete = 1;
        $purchase_item->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}