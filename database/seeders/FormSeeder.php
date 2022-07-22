<?php

namespace Database\Seeders;

use App\Models\v1\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($i = Form::first()) {
            $i->delete();
        }
        Form::insert([
            [
                'name' => 'Kukah Foundation',
                'slug' => str('Kukah Foundation')->slug(),
            ],
        ]);
    }
}
