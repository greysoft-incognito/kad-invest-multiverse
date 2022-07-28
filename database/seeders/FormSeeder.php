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
                'title' => 'The Kukah Prize for Young Innovators',
                'logo' => 'https://greysoft.ng/pe/kuklogo.png',
                'banner' => 'https://img.freepik.com/free-photo/system-developers-analyzing-code-wall-screen-tv-looking-errors-while-team-coders-collaborate-artificial-intelligence-project-programmers-working-together-machine-learning-software_482257-41819.jpg?w=2000',
                'banner_title' => 'The Kukah Prize for Young Innovators',
                'banner_info' => 'The Kukah Prize for Young Innovators is intended to support and spur young and innovative Africans whose ideas embody the potential to transform their communities and the world around us.                ',
                'deadline' => '2022-08-10',
                'socials' => json_encode([
                    'whatsapp' => '07060998702',
                    'facebook' => 'http://facebook.com/thekukahprize',
                    'twitter' => 'http://twitter.com/thekukahprize',
                    'linkedin' => 'http://linkedin.com/in/thekukahprize',
                    'email' => 'mailto:thekukahprize@greysoft.ng',
                    'other' => 'http://facebook.com/thekukahprize',
                ]),
            ],
        ]);
    }
}