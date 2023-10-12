<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /*
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'nickname' => $this->nickname,
            'phone' => $this->phone,
            'type' => $this->type,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'status' => $this->status,
            'age' => $this->age,
            'location' => $this->location,
            'visibility' => $this->visibility,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($request->roomOwnerHide) {
            unset(
                $data['phone'],
                $data['type'],
                $data['email'],
                $data['email_verified_at'],
                $data['status'],
                $data['age'],
                $data['location'],
                $data['visibility'],
                $data['created_at'],
                $data['updated_at']
            );
        } else if ($request->roomOwnerShow) {
            unset(
                $data['id'],
                $data['nickname'],
                $data['phone'],
                $data['type'],
                $data['email'],
                $data['email_verified_at'],
                $data['status'],
                $data['age'],
                $data['location'],
                $data['visibility'],
                $data['created_at'],
                $data['updated_at']
            );
        }
        elseif ($request->searchUser || $request->chatUser) {
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
        } elseif ($request->searchUserV2) {
            unset(
                $data['phone'],
                $data['email'],
                $data['email_verified_at'],
                $data['status'],
                $data['age'],
                $data['location'],
                $data['visibility'],
            );
        }
        elseif ($request->showUser) {
            unset(
                $data['type'],
                $data['email_verified_at'],
                $data['status'],
                $data['visibility'],
            );
        } elseif ($request->userConfiguration || $request->configureEdit) {
            unset(
                $data['firstname'],
                $data['lastname'],
                $data['phone'],
                $data['type'],
                $data['email'],
                $data['email_verified_at'],
                $data['age'],
                $data['location'],
            );
        }

        return $data;
    }
}
