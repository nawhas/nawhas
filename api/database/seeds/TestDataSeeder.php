<?php

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Reciter;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(EntityManager $em)
    {
        $reciter = new Reciter('Nadeem Sarwar', 'Top Reciter');
        $album = new Album($reciter, 'Ya Hussain', 2019);

        $em->persist($reciter, $album);
        app('em')->flush();
    }
}
