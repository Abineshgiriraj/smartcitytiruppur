<?php
namespace App\Http\Controllers;

use App\Models\ProductSpare;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class SpareRequestController extends Controller
{
    public function index(Request $request)
    {
        return view('content.spare_request.list');
    }

    public function add()
    {
        return view('content.spare_request.form');
    }

    public function edit($id)
    {
        $productspare = ProductSpare::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        return view('content.spare_request.form', compact('productspare'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'product_group' => 'required|string|max:255',
            'spare_groups' => 'required|array',
        ]);

        $spare_group = implode(',', $request->spare_groups);

        ProductSpare::create([
            'unique_id' => unique_id(),
            'product_group' => $request->product_group,
            'spare_group' => $spare_group, // convert array to comma-separated string
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Product created successfully!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_group' => 'required|string|max:255',
            'spare_groups' => 'required|array',
        ]);

        $spare_group = implode(',', $request->spare_groups);
        // dd($spare_group);
        $product = ProductSpare::findOrFail($id);

        $product->update([
            'product_group' => $request->product_group,
            'spare_group' => $spare_group,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
    }

    public function destroy($id)
    {
        $product = ProductSpare::where('unique_id', $id)->firstOrFail();
        // dd($id);
        $product->update(['is_delete' => 1]);
        return response()->json(['success' => true, 'message' => 'Product deleted successfully!']);
    }

    public function getCategoriesByGroup(Request $request)
    {
        $group_id = $request->input('group_id');

        $categories = DB::table('product_spare')
            ->where('group_id', $group_id)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('category_name')
            ->get();

        $options = '<option value="">Select Product Category</option>';
        foreach ($categories as $category) {
            $options .= '<option value="' . $category->unique_id . '">' . $category->category_name . '</option>';
        }

        return response()->json(['options' => $options]);
    }
}


function get_spare_group_names($ids)
{
    if (!$ids)
        return '';

    $idArray = explode(',', $ids);
    $names = DB::table('spare_group')
        ->whereIn('unique_id', $idArray)
        ->pluck('group_name') // use correct column name
        ->toArray();

    return implode(', ', $names);
}
