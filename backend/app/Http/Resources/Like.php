<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Like extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this -> id,
            'likeable_id' => $this -> likeable_id,
            'user' => $this -> user,
            'created_at' => $this -> created_at->diffForHumans(),
            'updated_at' => $this -> updated_at->diffForHumans(),
        ];

    }
}
