<?php

namespace App\Http\Controllers;

use App\Models\AccessGate;
use Illuminate\Http\Request;

class AccessGateController extends Controller
{
    public function index()
    {
        $access_gates = AccessGate::where('is_delete', 0)->get();
        return view('content/access_gate.list', compact('access_gates'));
    }
    public function add(){

      return view('content/access_gate.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
             'unique_id'=>unique_id(),
            'access_gate' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'required|boolean',
        ]);

        AccessGate::create($data);

        return response()->json(['success' => true, 'message' => 'Access Gate created successfully']);
    }

    public function edit($id)
    {
        $access_gate = AccessGate::findOrFail($id);
        return view('content/access_gate.edit', compact('access_gate'));
    }

    public function update(Request $request, $id)
    {
        $access_gate = AccessGate::findOrFail($id);

        $data = $request->validate([
            'access_gate' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $access_gate->update($data);

        return redirect()->route('access_gate.list')->with('success', 'Access Gate updated successfully!');
    }




    public function destroy($id)
    {
      $access_gate = AccessGate::find($id);

      if (!$access_gate) {
          return response()->json(['success' => false, 'message' => 'Center not found.']);
      }

      // Soft delete by setting is_delete flag
      $access_gate->is_delete = 1;
      $access_gate->save();

      return response()->json(['success' => true, 'message' => 'Deleted successfully!']);
    }
}