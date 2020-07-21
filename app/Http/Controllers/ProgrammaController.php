<?php

namespace App\Http\Controllers;

use App\Programma;
use App\Serie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgrammaController extends Controller
{
    //dettagli relativi al programma {id}, anche data/ora/canale di messa in onda
    public function show($id)
    {
        $programma = Programma::findOrFail($id);
        if($programma->genere_id) {
            $genere = $programma->genere()->first();
            $programma["genere_nome"] = $genere->nome;
        }
        if($programma->serie_id) {
            $serie = $programma->serie()->first();
            $programma["serie_nome"] = $serie->nome;
        }
        
        $programma["palinsesto"] = $programma->palinsesto()->get();
        return $programma;

    }

    //input: tutti i dati richiesti dal db, output: url per leggere il dettaglio del programma
    //solo admin
    public function store(Request $request, $sid)
    {
        //input: nome, tipologia, descrizione, immagine, link_approfondimento, numero_stagione, numero_puntata, genere_id, serie_id

        $validatedData = Validator::make($request->all(), [
            'nome' => 'required',
            'tipologia' => 'required',
            'descrizione' => 'required',
            'immagine' => 'required',
            'link_approfondimento' => 'required'
        ]);

        if($validatedData->fails()){
            return response()->json(['message' => 'Campi mancanti'], 422);
        }
        
        $nome = $request->nome;
        $tipologia = $request->tipologia;
        $descrizione = $request->descrizione;
        $immagine = $request->immagine;
        $link_approfondimento = $request->link_approfondimento;

        //check se esiste il genere
        $genere_id = $request->genere_id;

        //controllo se vengono passati i dati relativi alla prima messa in onda sul sistema
        if($request->has('canale_id') && $request->has('ora_inizio') && $request->has('ora_fine')) {
            $canale_id = $request->canale_id;
            $ora_inizio = Carbon::createFromFormat('Y-m-d H:i:s', $request->ora_inizio);
            $ora_fine = Carbon::createFromFormat('Y-m-d H:i:s', $request->ora_fine);
        }

        if($tipologia == 2)
        {
            $numero_stagione = $request->numero_stagione;
            $numero_puntata = $request->numero_puntata;
            if($request->serie_id) {
                $serie_id = $request->serie_id;
                $serie = Serie::find($serie_id);
            } else {
                $serie_nome = $request->serie_nome;
                $serie = new Serie;
                $serie->nome = $serie_nome;
                $serie->save();
            }
        }

        $programma = new Programma;
        $programma->nome = $nome;
        $programma->tipologia = $tipologia;
        $programma->descrizione = $descrizione;
        $programma->immagine = $immagine;
        $programma->link_approfondimento = $link_approfondimento;
        $programma->genere_id = $genere_id;

        if($tipologia == 2)
        {
            $programma->numero_stagione = $numero_stagione;
            $programma->numero_puntata = $numero_puntata;
            if($serie) {
                $programma->serie_id = $serie->id;
            }
        }


        $programma->save();

        if($request->has('canale_id') && $request->has('ora_inizio') && $request->has('ora_fine')) {
            $programma->palinsesto()->attach($canale_id, ['ora_inizio' => $ora_inizio, 'ora_fine' => $ora_fine]);
        }

        return $programma;

    }

    //modifica programma {id}, stesso payload di post
    //solo admin
    public function update(Request $request, $sid, $id)
    {

        $validatedData = Validator::make($request->all(), [
            'nome' => 'required',
            'tipologia' => 'required',
            'descrizione' => 'required',
            'immagine' => 'required',
            'link_approfondimento' => 'required'
        ]);

        if($validatedData->fails()){
            return response()->json(['message' => 'Campi mancanti'], 422);
        }

        $programma = Programma::findOrFail($id);  
        
        $nome = $request->nome;
        $tipologia = $request->tipologia;
        $descrizione = $request->descrizione;
        $immagine = $request->immagine;
        $link_approfondimento = $request->link_approfondimento;

        $genere_id = $request->genere_id;

        if($tipologia == 2)
        {
            $numero_stagione = $request->numero_stagione;
            $numero_puntata = $request->numero_puntata;
            if($request->serie_id) {
                $serie_id = $request->serie_id;
                $serie = Serie::find($serie_id);
            } else {
                $serie_nome = $request->serie_nome;
                $serie = new Serie;
                $serie->nome = $serie_nome;
                $serie->save();
            }
        }

        $programma->nome = $nome;
        $programma->tipologia = $tipologia;
        $programma->descrizione = $descrizione;
        $programma->immagine = $immagine;
        $programma->link_approfondimento = $link_approfondimento;
        $programma->genere_id = $genere_id;

        if($tipologia == 2)
        {
            $programma->numero_stagione = $numero_stagione;
            $programma->numero_puntata = $numero_puntata;
            //controllo se esiste quella serie
            if($serie) {
                $programma->serie_id = $serie->id;
            }
        }


        $programma->save();

        return $programma;
    }

    //se programma {id} fa parte di una serie, restituire struttura con tutti gli episodi della serie da trenta giorni nel passato a sette nel futuro
    public function episodi($id)
    {
        $programma = Programma::findOrFail($id);

        //check se è una serie
        if($programma->tipologia == 2) {
            //ritorna la serie, comprensiva di programmi e palinsesto
            return $programma->serie()->first();
        }else{
            //se non è una serie, ritorno errore 400
            return response()->json(['message' => 'Non è una serie'], 400);
        }
    }
}
