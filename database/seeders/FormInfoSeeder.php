<?php

namespace Database\Seeders;

use App\Models\v1\Form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $form = Form::first();
        $form->infos()->insert([
            [
                'form_id' => $form->id,
                'title' => 'THE KUKAH PRIZE FOR YOUNG INNOVATORS',
                'subtitle' => null,
                'content' => 'The Kukah Prize for Young Innovators is an award of recognition for outstanding innovators in the field of technology presented to young Africans who show exceptional promise as developing leaders in digital transformation. Bright young minds with creative technological ideas/solutions to developmental challenges both in their communities, and the world at large will be evaluated by judges in the startup and tech space across the continent for the most innovative digital ideas/solutions in the form of startups or initial concepts meeting global developmental challenges.',
                'list' => null,
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => false,
                'image' => '',
                'template' => null,
                'position' => 'left',
                'type' => 'text'
            ], [
                'form_id' => $form->id,
                'title' => 'Prizes',
                'subtitle' => '',
                'content' => null,
                'list' => json_encode([
                    [
                        'title' => 'First Position',
                        'content' => '₦2,000,000',
                    ], [
                        'title' => 'Second Position',
                        'content' => '₦1,000,000',
                    ], [
                        'title' => 'Third Position',
                        'content' => '₦500,000',
                    ]
                ]),
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => true,
                'image' => '',
                'template' => 'prize',
                'position' => 'left',
                'type' => 'list'
            ], [
                'form_id' => $form->id,
                'title' => 'The Kukah Centre (TKC)',
                'subtitle' => null,
                'content' => 'The Kukah Centre (TKC) is a Nigeria-based policy research institute, founded by Most Rev. Matthew Hassan Kukah, Bishop of the Catholic Diocese of Sokoto. The Centre has offices in Abuja and Kaduna. It treats political leadership as a collaborative exercise that requires multiple governance structures at various levels – individuals, households, small businesses, the organized private sector, NGOs and government. Interfaith dialogue is at the core of the Centre’s work and involves actively promoting conversations among Nigeria’s faith communities, as well as between leaders in faith and public policy. The Kukah Centre aspires to become Nigeria’s leading institution for the promotion of an active and engaged citizenry by providing support for inclusive dialogue and advocacy initiatives.',
                'list' => null,
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => false,
                'image' => 'https://greysoft.ng/pe/Kuk.jpeg',
                'template' => null,
                'position' => 'left',
                'type' => 'text'
            ], [
                'form_id' => $form->id,
                'title' => 'Live Award Ceremony',
                'subtitle' => 'Aug 31st, 2022',
                'content' => 'Three selected winners will receive the Kukah prize for young innovators in the Federal Capital Territory(FCT), Nigeria.',
                'list' => null,
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => false,
                'image' => 'https://greysoft.ng/pe/group-asia-young-creative-people-smart-casual-wear-discussing-business-brainstorming.jpg',
                'template' => null,
                'position' => 'left',
                'type' => 'text'
            ], [
                'form_id' => $form->id,
                'title' => 'Establishment and Support',
                'subtitle' => null,
                'content' => 'This award is established by The Kukah Center and sponsored by Greysoft Technologies. This maiden edition will be presented to winners during the 70th Birthday Ceremony of His Lordship, Bishop Matthew Hassan Kukah on the 31st August 2022. The winners of this award will in addition, have various opportunities to intern with organizations such as; Microsoft and Google.',
                'list' => null,
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => false,
                'image' => null,
                'template' => null,
                'position' => 'left',
                'type' => 'text'
            ], [
                'form_id' => $form->id,
                'title' => 'Submit your idea',
                'subtitle' => null,
                'content' => 'Ready to enter? Click here to submit your idea.',
                'list' => null,
                'icon' => null,
                'icon_color' => null,
                'increment_icon' => false,
                'image' => null,
                'template' => null,
                'position' => 'left',
                'type' => 'cta'
            ], [
                'form_id' => $form->id,
                'title' => 'Eligibility and Selection Criteria',
                'subtitle' => null,
                'content' => null,
                'list' => json_encode([
                    'The nominee shall not have reached their 31st birthday by the time the award is received.',
                    'Nominees must be Nigerian (Maiden Edition).',
                    'The award will be presented to an individual whose innovative work demonstrates high levels of excellence in technology.',
                    'The award winner will be selected based on the candidate\'s research\'s outstanding quality, novelty, and significance.',
                    'We encourage women and persons with disabilities to submit entries. We are willing to provide reasonable accommodations for persons with disabilities where needed.',
                    'Nominees are eligible for a single submission; multiple submissions would automatically disqualify them.',
                    'Female candidates are encouraged to apply.',
                    'Entry submissions are FREE.',
                ]),
                'icon' => 'fas fa-check-circle',
                'icon_color' => '#db9200',
                'increment_icon' => false,
                'image' => null,
                'template' => null,
                'position' => 'right',
                'type' => 'list'
            ], 
        ]);
    }
}