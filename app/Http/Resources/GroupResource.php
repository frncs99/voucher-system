<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'id' => $this->group_id,
            'name' => $this->name,
            'added_date' => date_format($this->created_at, "M d, Y h:i A"),
            'deleted' => $this->trashed() != null ? true : false,
        ];
    }
}
