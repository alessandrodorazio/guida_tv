<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canale extends Model
{
    //
    protected $table='canali';
    protected $fillable = ['nome', 'uri'];

    public function palinsesto()
    {
        return $this->belongsToMany(Programma::class, 'palinsesto', 'canale_id', 'programma_id')->withPivot(['ora_inizio', 'ora_fine']);
    }
}
