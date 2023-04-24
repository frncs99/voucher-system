<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupAdminResource extends JsonResource
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
            'group_admin_id' => $this->group_admin_id,
            'group_id' => $this->group_id,
            'group_name' => $this->group ? $this->group->name : null,
            'email' => $this->user ? $this->user->email : null,
            'name' => $this->user ? $this->user->name : null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
