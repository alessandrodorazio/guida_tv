<?php

namespace App\Http\Controllers;

use App\Canale;
use App\Programma;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PalinsestoController extends Controller
{

    //?{FILTER}[first={m}[&last={n}]]
    //FILTER può contenere tutti i parametri previsti per la ricerca di programmi (titolo, genere, canali, ecc.)
    //se non ci sono criteri di ricerca, la richiesta deve essere respinta
    //restituiire url per accedere al dettaglio di tutti i programmi corrispondenti ai criteri
    public function ricerca()
    {
        $programmi = Programma::with('palinsesto');
        $requestIsValid = false;

        if(isset($_GET['titolo'])) { //testo
            $programmi->where('nome', $_GET['titolo']);
            $requestIsValid = true;
        }

        if(isset($_GET['genere_id'])) { //intero
            $programmi->where('genere_id', $_GET['genere_id']);
            $requestIsValid = true;
        }

        if(isset($_GET['serie_id'])) { //cerca per nome della serie
            $programmi->where('serie_id', $_GET['serie_id']);
            $requestIsValid = true;
        }

        if(! $requestIsValid) {
            return response()->json(['message' => 'Non è possibile effettuare una ricerca senza filtri'], 400);
        }
        
        $programmi = $programmi->orderBy('created_at', 'ASC')->get();

        if(isset($_GET['first'])) {
            if(isset($_GET['last'])) {
                $programmi = $programmi->slice($_GET['first'], $_GET['last'] + 1 - $_GET['first']);
            } else {
                $programmi = $programmi->slice($_GET['first']);
            }
        }



        return $programmi;

    }

    public function canaleOggi($canale) {
        //palinsesto di un determinato canale per una determinata data
        //check se esiste il canale, altrimenti restituire 404
        $canale = Canale::findOrFail($canale);

        //selezione della data odierna
        $data = Carbon::today();

        //query di ricerca
        $palinsesto = $canale->palinsesto()->where([
            ['palinsesto.ora_inizio', '>=', $data->startOfDay()->format('Y-m-d')], 
            ['palinsesto.ora_inizio', '<=', $data->endOfDay()->format('Y-m-d H:i:s')],
            ])->get();

        //ritornare palinsesto odierno
        return $palinsesto;
    }

    //palinsesto di tutti i canali per la data {data}
    //TODO vedere da specifica quali informazioni devono essere ritornate
    public function dataPersonalizzata($data)
    {
        //check se data valida
        $canali = Canale::select('id', 'nome', 'uri')->get();
        $data = Carbon::createFromFormat('Y-m-d', $data);

        //array contenente tutti i programmi, suddivisi per canale
        $palinsesto = array();
        //ricordo se ho trovato almeno un programma
        $found = false;

        //prendi dal db tutti i programmi con quella data
        foreach($canali as $canale) {
            $palinsesto[$canale->nome] = $canale->palinsesto()->where([
                ['ora_inizio', '>=', $data->startOfDay()->format('Y-m-d')], 
                ['ora_inizio', '<=', $data->endOfDay()->format('Y-m-d H:i:s')],
                ])->get();
            if(count($palinsesto[$canale->nome]) > 0) {
                $found = true;
            }
        }

        if(! $found) {
            return response()->json(['message' => 'Nessun programma trovato'], 404);
        }

        return $palinsesto;

        
    }

    //palinsesto di un determinato canale per una determinata data
    public function dataCanalePersonalizzati($data = null, $canale)
    {
        //check se esiste il canale
        $canale = Canale::findOrFail($canale);

        //parse della data per la query
        $data = ($data===null)?Carbon::today():Carbon::createFromFormat('Y-m-d', $data);
        //query di ricerca
        $palinsesto = $canale
        ->palinsesto()
        ->where([
            ['ora_inizio', '>=', $data->startOfDay()->format('Y-m-d')], 
            ['ora_inizio', '<=', $data->endOfDay()->format('Y-m-d H:i:s')],
            ])->get();

        //ritornare palinsesto
        return $palinsesto;
    }


}
