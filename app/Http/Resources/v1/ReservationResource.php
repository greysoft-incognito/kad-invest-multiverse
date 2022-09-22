<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'space_id' => $this->space_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'qr' => route('form.data.qr', ['reservation', $this->id]),
            'status' => $this->status,
            'user_type' => $this->user_type,
            'user' => $this->user_type === 'guest' ? $this->guest : $this->user,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}