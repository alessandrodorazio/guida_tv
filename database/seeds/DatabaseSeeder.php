<?php

use App\Canale;
use App\Genere;
use App\Programma;
use App\Serie;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        /*
        Canale::insert([
            [
                'nome' => 'Rai 1',
                'uri' => 'link/rai1',
                'created_at' => Carbon::now()
            ],
            [
                'nome' => 'Rai 2',
                'uri' => 'link/rai2',
                'created_at' => Carbon::now()
            ]
        ]);
        */

        Genere::insert([
            ['nome' => 'Animazione'],
            ['nome' => 'Avventura'],
            ['nome' => 'Commedia'],
            ['nome' => 'Documentario'],
            ['nome' => 'Horror'],
            ['nome' => 'Poliziesco'],
            ['nome' => 'Thriller']
        ]);

        /*
        Serie::insert([
            ['nome' => 'Don Matteo'],
            ['nome' => 'Il giovane Montalbano']
        ]);

        Programma::insert([
            [
                'nome' => 'Don Matteo',
                'tipologia' => 2,
                'created_at' => Carbon::now()
            ],
            [
                'nome' => 'Quo vado',
                'tipologia' => 1,
                'created_at' => Carbon::now()
            ]
        ]);

        $rai1 = Canale::where('nome', 'Rai 1')->first();
        $rai1->palinsesto()->attach([1 => ['ora_inizio' => Carbon::now(), 'ora_fine' => Carbon::now()->addHour()]]);
        */
    }
}
