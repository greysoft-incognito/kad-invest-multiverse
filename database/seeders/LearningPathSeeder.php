<?php

namespace Database\Seeders;

use App\Models\v1\Form;
use App\Models\v1\Portal\LearningPath;
use App\Models\v1\Portal\Portal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LearningPath::truncate();
        Portal::get()->map(function ($portal) {
            $portal->reg_form && $portal->reg_form->learning_paths()->insert([
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Product Design',
                    'subtitle' => 'Product Design',
                    'title' => 'Product Design',
                    'image' => asset('images/pe/design.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => 'https://www.youtube.com/watch?v=MCy6WtYjI-8&feature=emb_imp_woyt',
                    'price' => 115000.00,
                ],
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Frontend Engineering',
                    'subtitle' => 'Frontend Engineering',
                    'title' => 'Frontend Engineering',
                    'image' => asset('images/pe/frontend.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => 'https://www.youtube.com/embed/3cEhOYvO9SM"',
                    'price' => 115000.00,
                ],
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Backend Engineering',
                    'subtitle' => 'Backend Engineering',
                    'title' => 'Backend Engineering',
                    'image' => asset('images/pe/backend.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => 'https://www.youtube.com/embed/9IczM7y0Yc8',
                    'price' => 115000.00,
                ],
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Data Science',
                    'subtitle' => 'Data Science',
                    'title' => 'Data Science',
                    'image' => asset('images/pe/data.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => 'https://www.youtube.com/embed/NJvncopOC88',
                    'price' => 115000.00,
                ],
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Digital Marketing',
                    'subtitle' => 'Digital Marketing',
                    'title' => 'Digital Marketing',
                    'image' => asset('images/pe/digital.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => 'https://www.youtube.com/embed/FVOfhCxJ6DU',
                    'price' => 50000.00,
                ],
                [
                    'learnable_type' => Form::class,
                    'learnable_id' => $portal->reg_form->id,
                    'description' => 'Blockchan Technology',
                    'subtitle' => 'Blockchan Technology',
                    'title' => 'Blockchan Technology',
                    'image' => asset('images/pe/blockchain.png'),
                    'video' => '',
                    'background' => '',
                    'component' => '',
                    'video_link' => null,
                    'price' => 115000.00,
                ],
            ]);
        });
    }
}
