<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\gateaccess;

class GateAccessInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.gateAccess.penaltyList');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.gateAccess.accessIn');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rfidData' => 'required',
            'pass_id' => 'required',
            'indatetime' => 'required',
            'gate_id' => 'required',
        ]);

        $get_access = gateaccess::create([
            'unique_id' => unique_id(),
            'rfid' => $request->rfidData,
            'pass_sub_id' => $request->pass_id,
            'in_out_datetime' => $request->indatetime,
            'gate_id' => $request->gate_id,
        ]);


        return response()->json(['get_access' => $get_access]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}