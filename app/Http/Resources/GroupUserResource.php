<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
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
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'left_active_count' => $this->left_active_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->user)
        ];

        if ($request->searchUser) {
            unset(
                $data['phone'],
                $data['type'],
                $data['email'],
                $data['email_verified_at'],
                $data['status'],
                $data['age'],
                $data['location'],
                $data['visibility'],
            );
        }

        return $data;
    }
}
