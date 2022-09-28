<?php

namespace Database\Seeders;

use App\Models\v1\Portal\PortalPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PortalPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        PortalPage::truncate();
        PortalPage::insert([
            [
                'portal_id' => 1,
                'slug' => 'bootcamp-welcome',
                'title' => 'Bootcamp 1',
                'description' => 'Bootcamp 1 Portal',
                'keywords' => 'Bootcamp 1 Portal',
                'content' => 'Bootcamp 1 Portal',
                'footer_group' => 'services',
                'component' => 'Portal1',
                'image' => null,
                'index' => true,
                'in_footer' => true,
                'in_navbar' => true,
            ], [
                'portal_id' => 2,
                'slug' => 'bootcamp-details',
                'title' => 'Bootcamp 2',
                'description' => 'Bootcamp Details',
                'keywords' => 'Bootcamp Details',
                'content' => 'Bootcamp Details',
                'footer_group' => 'services',
                'component' => 'Portal2',
                'image' => null,
                'index' => true,
                'in_footer' => true,
                'in_navbar' => true,
            ], [
                'portal_id' => 1,
                'slug' => str('Artificial Intelligence')->slug(),
                'title' => 'Artificial Intelligence',
                'description' => 'Assertively parallel task synergistic deliverables after high-quality.',
                'keywords' => 'Artificial Intelligence',
                'content' => fake()->paragraphs(3, true),
                'footer_group' => 'services',
                'component' => 'Portal2',
                'image' => asset('images/pe/depertment-1.png'),
                'index' => false,
                'in_footer' => true,
                'in_navbar' => true,
            ], [
                'portal_id' => 1,
                'slug' => str('Civil Engineering')->slug(),
                'title' => 'Civil Engineering',
                'description' => 'Assertively parallel task synergistic deliverables after high-quality.',
                'keywords' => 'Civil Engineering',
                'content' => fake()->paragraphs(3, true),
                'footer_group' => 'company',
                'component' => 'Portal2',
                'image' => asset('images/pe/depertment-2.png'),
                'index' => false,
                'in_footer' => true,
                'in_navbar' => true,
            ], [
                'portal_id' => 1,
                'slug' => str('Business Studies')->slug(),
                'title' => 'Business Studies',
                'description' => 'Assertively parallel task synergistic deliverables after high-quality.',
                'keywords' => 'Business Studies',
                'content' => fake()->paragraphs(3, true),
                'footer_group' => 'business',
                'component' => 'Portal2',
                'image' => asset('images/pe/depertment-3.png'),
                'index' => false,
                'in_footer' => true,
                'in_navbar' => true,
            ],
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
