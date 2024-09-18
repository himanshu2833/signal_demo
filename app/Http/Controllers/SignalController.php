<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signal;

class SignalController extends Controller
{
    public function index(){
        $signals = Signal::all();
        return view('signals.index', compact('signals'));
    }

    public function store(Request $request){
        // dd($request);
        $request->validate([
            'sequence' => 'required|array',
            'green_internal' => 'required|integer',
            'yellow_internal' => 'required|integer',
        ]);

        $signal = Signal::create($request->all());

        return response()->json(['signal' => $signal, 'success' => 'Created sucessfully']);
    }

    public function edit($id){
        $signal = Signal::find($id);
        return response()->json($signal);
    }

    public function update(Request $request ,$id){
        $request->validate([
            'sequence' => 'required|array',
            'green_internal' => 'required|integer',
            'yellow_internal' => 'required|integer',
        ]);

        $signal = Signal::find($id);

        $signal->update($request->all());

        return response()->json(['signal' => $signal, 'success' => 'Updated sucessfully']);
    }

    public function destroy($id){
        $signal = Signal::find($id);
        $signal->delete();
        return response()->json(['success' => 'Deleted sucessfully']);
    }
}
