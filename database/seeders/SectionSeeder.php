<?php

namespace Database\Seeders;

use App\Models\v1\Portal\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Section::truncate();
        Section::insert([
            // Sections for portal page 1
            [
                'portal_page_id' => 2,
                'title' => 'The Best Program to Enroll for Exchange',
                'title_highlight' => 'Program to',
                'subtitle' => fake()->sentence(10),
                'minititle' => '',
                'type' => 'banner',
                'image' => asset('images/pe/hero-banner-1.jpg'),
                'image2' => asset('images/pe/hero-banner-2.jpg'),
                'background' => asset('images/pe/hero-bg.svg'),
                'image_position' => 'right',
                'component' => 'Hero',
                'link' => json_encode(['title' => 'Find Courses', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 2,
                'title' => 'Online Classes For Remote Learning.',
                'title_highlight' => 'For Remote',
                'subtitle' => 'CATEGORIES',
                'minititle' => '',
                'type' => 'cards',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Programs',
                'link' => json_encode([]),
                'content' => fake()->words(20, true),
                'video_link' => '',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 2,
                'title' => 'Over 10 Years in Distant learning for Skill Development.',
                'title_highlight' => 'Distant learning',
                'subtitle' => 'About Us',
                'minititle' => '',
                'type' => 'banner',
                'image' => asset('images/pe/about-shape-1.svg'),
                'image2' => asset('images/pe/about-shape-2.svg'),
                'background' => asset('images/pe/about-banner.jpg'),
                'image_position' => 'right',
                'component' => 'About',
                'link' => json_encode([]),
                'content' => fake()->words(20, true),
                'video_link' => '',
                'list' => json_encode([
                    'Expert Trainers',
                    'Online Remote Learning',
                    'Lifetime Access',
                ]),
            ], [
                'portal_page_id' => 2,
                'title' => 'Pick A Course To Get Started.',
                'title_highlight' => 'Course To',
                'subtitle' => 'POPULAR COURSES',
                'minititle' => '',
                'type' => 'cards',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Courses',
                'link' => json_encode(['title' => 'Browse More Courses', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => fake()->words(20, true),
                'video_link' => '',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 2,
                'title' => 'Learn More.',
                'title_highlight' => null,
                'subtitle' => 'Learn More',
                'minititle' => '',
                'type' => 'video',
                'image' => asset('images/pe/video-banner.jpg'),
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Video',
                'link' => json_encode([]),
                'content' => '',
                'video_link' => '#',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 2,
                'title' => 'What you should know.',
                'title_highlight' => null,
                'subtitle' => 'Statistics',
                'minititle' => '',
                'type' => 'cards',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Counter',
                'link' => json_encode([]),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 2,
                'title' => 'Get News With Greysoft.',
                'title_highlight' => null,
                'subtitle' => 'LATEST ARTICLES',
                'minititle' => '',
                'type' => 'cards',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Blog',
                'link' => json_encode([]),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([]),
            ],
            // Sections for portal page 1
            [
                'portal_page_id' => 1,
                'title' => 'Start Your Future Education',
                'title_highlight' => null,
                'subtitle' => fake()->sentence(10),
                'minititle' => '',
                'type' => 'banner',
                'image' => asset('images/pe/hero-banner.png'),
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Hero',
                'link' => json_encode(['title' => 'Discover More', 'url' => '#', 'target' => '_self', 'type' => 'primary'], ),
                'content' => fake()->sentence(10),
                'video_link' => '',
                'list' => json_encode([]),
            ],
            [
                'portal_page_id' => 1,
                'title' => 'We Help to Create Possibility & Success in Your Career!',
                'title_highlight' => 'Possibility & Success',
                'subtitle' => 'Learn More',
                'minititle' => '',
                'type' => 'video',
                'image' => asset('images/pe/about-banner.png'),
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'About',
                'link' => json_encode(['title' => 'Get Started Today', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => 'Continually administrate process-centric human capital rather than bleeding-edge methodologies. Distinctively supply accurate methods of empowerment before.',
                'video_link' => '#',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 1,
                'title' => 'We Have Most of Popular Departments.',
                'title_highlight' => null,
                'subtitle' => 'Departments',
                'minititle' => '',
                'type' => 'pages',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Departments',
                'link' => json_encode(['title' => 'View All Departments', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([3, 4, 5]),
            ], [
                'portal_page_id' => 1,
                'title' => 'Introduce with Our Famous Teachers.',
                'title_highlight' => null,
                'subtitle' => 'Instructors',
                'minititle' => '',
                'type' => 'cards',
                'image' => null,
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Team',
                'link' => json_encode(['title' => 'View All Teachers', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([]),
            ], [
                'portal_page_id' => 1,
                'title' => 'Create Free Account & Get Registered.',
                'title_highlight' => null,
                'subtitle' => 'Get Registered',
                'minititle' => '',
                'type' => 'cta',
                'image' => asset('images/pe/cta-banner.png'),
                'image2' => null,
                'background' => null,
                'image_position' => 'right',
                'component' => 'Cta',
                'link' => json_encode(['title' => 'Register Now', 'url' => '#', 'target' => '_self', 'type' => 'primary']),
                'content' => '',
                'video_link' => '',
                'list' => json_encode([]),
            ],
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
