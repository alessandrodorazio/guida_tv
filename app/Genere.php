<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genere extends Model
{
    //
    protected $table='generi';
    protected $fillable = ['nome'];
    public $timestamps = false;
}
