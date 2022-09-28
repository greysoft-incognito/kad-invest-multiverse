<?php

namespace Database\Seeders;

use App\Models\v1\GenericFormField;
use App\Models\v1\Portal\Portal;
use Illuminate\Database\Seeder;

class PortalGenericFormFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Portal::where('allow_registration', true)->get()->each(function ($portal) {
            $portal->forms->each(function ($form) {
                GenericFormField::insert([
                    [
                        'form_id' => $form->id,
                        'label' => 'Fullame',
                        'alias' => null,
                        'name' => 'fullname',
                        'field_id' => 'fullname',
                        'element' => 'input',
                        'type' => 'text',
                        'options' => null,
                        'required' => true,
                        'required_if' => null,
                        'key' => false,
                        'restricted' => false,
                        'value' => '',
                        'hint' => null,
                        'min' => '3',
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    // [
                    //     'form_id' => $form->id,
                    //     'label' => 'Email Address',
                    //     'alias' => null,
                    //     'name' => 'email',
                    //     'field_id' => 'email',
                    //     'element' => 'input',
                    //     'type' => 'email',
                    //     'options' => null,
                    //     'required' => true,
                    //     'required_if' => null,
                    //     'key' => true,
                    //     'restricted' => false,
                    //     'value' => '',
                    //     'hint' => 'Please enter a your email address',
                    //     'min' => null,
                    //     'max' => null,
                    //     'compare' => null,
                    //     'custom_error' => null,
                    // ],
                    [
                        'form_id' => $form->id,
                        'label' => 'Phone Number',
                        'alias' => null,
                        'name' => 'phone',
                        'field_id' => 'phone',
                        'element' => 'input',
                        'type' => 'tel',
                        'options' => null,
                        'required' => true,
                        'required_if' => null,
                        'key' => true,
                        'restricted' => false,
                        'value' => '',
                        'hint' => null,
                        'min' => null,
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    [
                        'form_id' => $form->id,
                        'label' => 'Gender',
                        'alias' => null,
                        'name' => 'gender',
                        'field_id' => 'gender',
                        'element' => 'select',
                        'type' => 'text',
                        'options' => json_encode([
                            ['label' => 'Male', 'value' => 'male'],
                            ['label' => 'Female', 'value' => 'female'],
                            ['label' => 'Other', 'value' => 'other'],
                            ['label' => 'Prefere not to say', 'value' => ''],
                        ]),
                        'required' => false,
                        'required_if' => null,
                        'key' => false,
                        'restricted' => false,
                        'value' => 'male',
                        'hint' => null,
                        'min' => null,
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    [
                        'form_id' => $form->id,
                        'label' => 'Select Course',
                        'alias' => null,
                        'name' => 'course',
                        'field_id' => 'course',
                        'element' => 'select',
                        'type' => 'text',
                        'options' => json_encode([
                            ['label' => 'UI/UX (Product Designs)', 'value' => 'ui_ux'],
                            ['label' => 'Frontend Developement', 'value' => 'frontend'],
                            ['label' => 'Backend Developement', 'value' => 'backend'],
                            ['label' => 'Data Science', 'value' => 'data_science'],
                            ['label' => 'Digital Marketing', 'value' => 'digital_marketing'],
                        ]),
                        'required' => true,
                        'required_if' => null,
                        'key' => false,
                        'restricted' => false,
                        'value' => 'Physical',
                        'hint' => 'Choose the course you want to register for',
                        'min' => null,
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    [
                        'form_id' => $form->id,
                        'label' => 'Date of birth',
                        'alias' => null,
                        'name' => 'dob',
                        'field_id' => 'dob',
                        'element' => 'input',
                        'type' => 'date',
                        'options' => null,
                        'required' => true,
                        'required_if' => null,
                        'key' => false,
                        'restricted' => false,
                        'value' => '',
                        'hint' => null,
                        'min' => '3',
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    [
                        'form_id' => $form->id,
                        'label' => 'Do you have any disabilities',
                        'alias' => null,
                        'name' => 'disabilities',
                        'field_id' => 'disabilities',
                        'element' => 'select',
                        'type' => 'text',
                        'options' => json_encode([
                            ['label' => 'No', 'value' => '0'],
                            ['label' => 'Yes', 'value' => '1'],
                        ]),
                        'required' => true,
                        'required_if' => null,
                        'key' => false,
                        'restricted' => false,
                        'value' => '0',
                        'hint' => null,
                        'min' => null,
                        'max' => null,
                        'compare' => null,
                        'custom_error' => null,
                    ],
                    [
                        'form_id' => $form->id,
                        'label' => 'If yes then specify',
                        'alias' => null,
                        'name' => 'disabilities_specify',
                        'field_id' => 'disabilities_specify',
                        'element' => 'input',
                        'type' => 'text',
                        'options' => null,
                        'required' => true,
                        'required_if' => 'disabilities=1',
                        'key' => false,
                        'restricted' => false,
                        'value' => '',
                        'hint' => null,
                        'min' => '3',
                        'max' => null,
                        'compare' => null,
                        'custom_error' => 'If you have a disaility please tell us little about it.',
                    ],
                ]);
            });
        });
    }
}
