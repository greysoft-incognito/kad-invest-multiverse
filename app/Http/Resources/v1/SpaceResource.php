<?php

namespace App\Http\Resources\v1;

use App\Services\AppInfo;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class SpaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = Gate::inspect('can-do', ['spaces.list']);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "size" => $this->size,
            "info" => $this->info,
            "price" => $this->price,
            "image_url" => $this->images['image'],
            "total_occupants" => $this->total_occupants,
            "available_spots" => $this->available_spots,
            $this->mergeWhen($response->allowed(), function () {
                return [
                    "reservations" => $this->reservations,
                    "users" => $this->users,
                ];
            }),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
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