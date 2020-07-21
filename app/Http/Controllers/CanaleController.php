<?php

namespace App\Http\Controllers;

use App\Canale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;

class CanaleController extends Controller
{
    //nome, ID e URI di tutti i canali
    public function index()
    {
        $canali = Canale::select('id', 'nome', 'uri')->get();
        return $canali;
    }

    //nuovo canale nel database, in input nome. in output il link per vedere il canale e il relativo palinsesto
    //solo admin
    public function store(Request $request, $sid)
    {
        $validatedData = Validator::make($request->all(), [
            'nome' => 'required',
        ]);

        if($validatedData->fails()){
            return response()->json(['message' => 'Campi mancanti'], 422);
        }
    
        $nome = $request->nome;

        $canale = new Canale;
        $canale->nome = $nome;
        $canale->save();

        $canale->uri = route('canale.alias.palinsesto.dataOdierna', ['id' => $canale->id]);
        $canale->save();

        return response()->json(['uri' => $canale->uri]);
    }

    //modifica dati del canale {ID}, stesso payload di store
    //solo admin
    public function update(Request $request, $sid, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'nome' => 'required',
        ]);

        if($validatedData->fails()){
            return response()->json(['message' => 'Campi mancanti'], 422);
        }
            
        $nome = $request->nome;

        $canale = Canale::findOrFail($id);
        $canale->nome = $nome;
        $canale->save();
     
        return response()->json(['uri' => $canale->uri]);
    }

}
