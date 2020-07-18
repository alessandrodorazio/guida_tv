<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Programma extends Model
{
    //
    protected $table='programmi';
    protected $fillable = ['nome', 'tipologia', 'descrizione', 'immagine', 'link_approfondimento', 'numero_stagione', 'numero_puntata', 'genere_id', 'serie_id'];
    protected $appends = ['palinsesto'];

    public function palinsesto() 
    {
        return $this->belongsToMany(Canale::class, 'palinsesto', 'programma_id', 'canale_id')->withPivot(['ora_inizio', 'ora_fine']);
    }

    public function genere()
    {
        return $this->belongsTo(Genere::class, 'genere_id');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }

    public function getPalinsestoAttribute() {
        return $this->palinsesto()->get();
    }

}
