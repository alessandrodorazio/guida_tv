<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    //
    protected $table='serie';
    protected $fillable = ['nome'];
    protected $appends = ['listaEpisodi'];
    
    public function episodi()
    {
        return $this->hasMany(Programma::class, 'serie_id');
    }

    public function getListaEpisodiAttribute()
    {
        return $this->episodi()->with('palinsesto')->get();
    }
}
