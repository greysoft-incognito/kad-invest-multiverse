<?php

namespace App\Http\Resources\v1;

use App\Services\AppInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'title' => $this->title,
            'external_link' => $this->external_link,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'banner_title' => $this->banner_title,
            'banner_info' => $this->banner_info,
            'template' => $this->template,
            'socials' => $this->socials,
            'deadline' => $this->deadline,
            'infos' => $this->infos,
            'fields' => $this->fields,
        ];
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