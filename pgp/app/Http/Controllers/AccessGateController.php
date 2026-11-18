<?php

namespace App\Http\Controllers;

use App\Models\AccessGate;
use Illuminate\Http\Request;

class AccessGateController extends Controller
{
    public function index()
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }

        $access_gates = AccessGate::where('is_delete', 0)->get();
        return view('content/access_gate.list', compact('access_gates'));
    }
    public function add(){

        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }

      return view('content/access_gate.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'access_gate' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'required|boolean',
        ]);

        // AccessGate::create($data);

	$access_gate = AccessGate::create([
	    'unique_id' => unique_id(),
	    'access_gate' => $request->access_gate,
	    'short_name' => $request->short_name,
	    'description' => $request->description,
	    'status' => $request->status
	]);

        return response()->json(['success' => true, 'message' => 'Access Gate created successfully']);
    }

    public function edit($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }

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

        return response()->json(['success' => true, 'message' => 'Access Gate updated successfully!']);
    }




    public function destroy($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        
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