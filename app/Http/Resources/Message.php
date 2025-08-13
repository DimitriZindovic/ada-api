<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'sender' => new User($this->whenLoaded('sender')),
            'group' => new Group($this->whenLoaded('group')),
            'createdAt' => $this->createdAt
        ];
    }
}
