<?php

namespace App\Http\Resources;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'check_in_date'  => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'user'           => new UserResource(User::findOrFail($this->user_id)),
            'room'           => new RoomResource(Room::findOrFail($this->room_id)),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at
        ];
    }
}
