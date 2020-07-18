<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RicercaRecente extends Model
{
    //
    protected $table='ricerche_recenti';
    protected $fillable = ['user_id', 'parametri'];
    protected $casts = [
        'parametri' => 'object'
    ];
}
