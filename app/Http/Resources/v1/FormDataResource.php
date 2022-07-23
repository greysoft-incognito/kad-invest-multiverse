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
        return collect([
            'id' => $this->id, 
            'form_id' => $this->form_id, 
        ])
        ->merge($this->data)
        ->merge(['fields' => $this->form->fields]);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return AppInfo::api();
    }
}