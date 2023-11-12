<?php

namespace App\Http\Resources;

use App\Models\MessagesBlob;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'body' => $this->body,
            'type' => $this->type,
            'messages_blob_id' => $this->messages_blob_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        return $data;
    }
}
