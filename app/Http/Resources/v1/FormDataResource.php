<?php

namespace App\Http\Resources\v1;

use App\Services\AppInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class FormDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $form = $this->form;
        $data = $this->data;
        $email_field = $form->fields()->where('type', 'email')->first();
        $fname_field = $form->fields()->where('name', 'like', '%firstname%')->orWhere('name', 'like', '%first_name%')->first();
        $lname_field = $form->fields()->where('name', 'like', '%lastname%')->orWhere('name', 'like', '%last_name%')->first();
        $fullname_field = $form->fields()->where('name', 'like', '%fullname%')->orWhere('name', 'like', '%full_name%')->first();
        $name_field = $form->fields()->where('name', 'like', '%name%')->first();

        $name = collect([
            $fname_field ? $data[$fname_field->name]??$this->id : '',
            $lname_field ? $data[$lname_field->name]??'' : '',
            $fullname_field && !$fname_field ? $data[$fullname_field->name]??"" : '',
            $name_field && !$fullname_field ? $data[$name_field->name]??'' : '',
        ])->filter(fn($name) => $name !=='')->implode(' ');

        return collect([
            'id' => $this->id,
            'name' => $name,
            'form_id' => $this->form_id,
            'email' => $data[$email_field->name],
            'qr' => route('form.data.qr', ['form', $this->id]),
            'scan_date' => $form->scan_date,
            'fields' => $this->form->fields,
        ])
        ->merge($this->data);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return AppInfo::with(['fields' => $this->form->fields]);
    }
}