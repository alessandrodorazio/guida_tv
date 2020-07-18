<?php

namespace App\Http\Controllers;

use App\Canale;
use App\Helper\ToUtf;
use App\Programma;
use App\Serie;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaiController extends Controller
{
    //
    

    public function import(){

        $client = new \GuzzleHttp\Client();
        $dataFinale = Carbon::today()->addDays(7);;
        for($dataCarbon = Carbon::today()->subDays(7); $dataCarbon < $dataFinale; $dataCarbon->addDay() ) {
            $res = $client->get('https://www.raiplay.it/palinsesto/app/old/' . $dataCarbon->format('d-m-Y') . '/canali.json');
            $resContent = json_decode($res->getBody()->getContents(), true);
            $palinsestoOdierno = $resContent[$dataCarbon->format('d-m-Y')];
            foreach($palinsestoOdierno as $canaleInfo) {
                if(Canale::where('nome', $canaleInfo['canale'])->count() == 0) {
                    $canale = new Canale;
                    $canale->nome = $canaleInfo['canale'];
                    $canale->save();
                } else {
                    $canale = Canale::where('nome', $canaleInfo['canale'])->first();
                }
                
                $palinsesto = $canaleInfo['palinsesto'][0];
                foreach($palinsesto['programmi'] as $programmaAnalizzato) {
                    if($programmaAnalizzato) {
                        $programma = new Programma;
                        if($programmaAnalizzato['stagione'] != "") { //è una serie?
                            $serie = Serie::where('nome', $programmaAnalizzato['isPartOf']['name'])->first();
                            if(! $serie) {
                                $serie = new Serie;
                                $serie->nome = $programmaAnalizzato['isPartOf']['name'];
                                $serie->save();
                            }
                            $programma->serie_id = $serie->id;
                            $programma->tipologia = 2;
                            $programma->nome = $programmaAnalizzato['titoloEpisodio'];
                            $programma->descrizione = utf8_encode(substr($programmaAnalizzato['description'], 0, 100));
                            if(is_numeric($programmaAnalizzato['stagione'])) {
                                $programma->numero_stagione = $programmaAnalizzato['stagione'];
                            }
                            if($programmaAnalizzato['episodio'] != "" && is_numeric($programmaAnalizzato['episodio'])) {
                                $programma->numero_puntata = $programmaAnalizzato['episodio'];
                            }
                        } else { // non è una serie
                            $programma->tipologia = 1;
                            $programma->nome = $programmaAnalizzato['name'];
                            $programma->descrizione = utf8_encode(substr($programmaAnalizzato['description'], 0, 100));
                        }
                        $programma->immagine = 'https://www.raiplay.it/cropgd/1200x600/' . $programmaAnalizzato['images']['landscape'];
                        $programma->save();
                        //inserimento nel palinsesto
                        $oraInizio = Carbon::createFromFormat('d/m/Y', $programmaAnalizzato['datePublished']);
                        if($programmaAnalizzato['timePublished']) {
                            $oraInizio = $oraInizio->setTime($programmaAnalizzato['timePublished'][0].$programmaAnalizzato['timePublished'][1],$programmaAnalizzato['timePublished'][3].$programmaAnalizzato['timePublished'][4]);
                        }
                        if($programmaAnalizzato['duration']) {
                            $oraFine = Carbon::createFromFormat('d/m/Y', $programmaAnalizzato['datePublished']);
                            $oraFine->setTime($programmaAnalizzato['timePublished'][0].$programmaAnalizzato['timePublished'][1],$programmaAnalizzato['timePublished'][3].$programmaAnalizzato['timePublished'][4]);
                            $oraFine->addHour($programmaAnalizzato['duration'][0].$programmaAnalizzato['duration'][1])->addMinutes($programmaAnalizzato['duration'][3].$programmaAnalizzato['duration'][4]);
                        }
                        $canale->palinsesto()->attach([$programma->id => ['ora_inizio' => $oraInizio, 'ora_fine' => $oraFine]]);
                    }
                }
            }
        }
        
        return response()->json(['message' => 'Importazione eseguita']);
    }

}