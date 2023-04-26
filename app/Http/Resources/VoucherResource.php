<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'voucher_id' => $this->voucher_id,
            'user_email' => $this->user ? $this->user->email : null,
            'user_name' => $this->user ? $this->user->name : null,
            'code' => $this->code,
            'added_date' => date_format($this->created_at, "M d, Y h:i A"),
        ];
    }
}
