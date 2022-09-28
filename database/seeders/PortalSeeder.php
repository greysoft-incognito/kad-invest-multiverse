<?php

namespace Database\Seeders;

use App\Models\v1\Guest;
use App\Models\v1\Portal\Portal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Portal::truncate();
        Portal::insert([
            [
                'name' => 'Bootcamp',
                'slug' => 'bootcamp',
                'description' => fake()->paragraph(2),
                'footer_info' => fake()->paragraph(2),
                'logo' => 'logo.png',
                'favicon' => 'favicon.png',
                'allow_registration' => true,
                'registration_model' => Guest::class,
                'reg_link_title' => 'Register',
                'login_link_title' => 'Login',
                'address' => fake()->address,
                'email' => fake()->email,
                'phone' => fake()->phoneNumber,
                'meta' => fake()->paragraph(2),
                'banner' => random_img('images/pe'),
                'footer_groups' => json_encode([
                    'services', 'company', 'business',
                ]),
                'socials' => json_encode([
                    ['type' => 'facebook', 'link' => 'https://web.facebook.com/InvestKaduna?_rdc=1&_rdr'],
                    ['type' => 'twitter', 'link' => 'https://twitter.com/InvestKaduna?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor'],
                    ['type' => 'instagram', 'link' => 'https://www.instagram.com/accounts/login/?next=/investkaduna/'],
                    ['type' => 'linkedin', 'link' => 'https://ng.linkedin.com/company/kadipa'],
                ]),
                'copyright' => 'Copyright 2022, Greysoft Technologies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bootcamp 2',
                'slug' => 'bootcamp-2',
                'description' => fake()->paragraph(2),
                'footer_info' => fake()->paragraph(2),
                'logo' => 'logo.png',
                'favicon' => 'favicon.png',
                'allow_registration' => true,
                'registration_model' => Guest::class,
                'reg_link_title' => 'Register',
                'login_link_title' => 'Login',
                'address' => fake()->address,
                'email' => fake()->email,
                'phone' => fake()->phoneNumber,
                'meta' => fake()->paragraph(2),
                'banner' => random_img('images/pe'),
                'footer_groups' => json_encode([
                    'services', 'company', 'business',
                ]),
                'socials' => json_encode([
                    ['type' => 'facebook', 'link' => 'https://web.facebook.com/InvestKaduna?_rdc=1&_rdr'],
                    ['type' => 'twitter', 'link' => 'https://twitter.com/InvestKaduna?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor'],
                    ['type' => 'instagram', 'link' => 'https://www.instagram.com/accounts/login/?next=/investkaduna/'],
                    ['type' => 'linkedin', 'link' => 'https://ng.linkedin.com/company/kadipa'],
                ]),
                'copyright' => 'Copyright 2022, Greysoft Technologies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
