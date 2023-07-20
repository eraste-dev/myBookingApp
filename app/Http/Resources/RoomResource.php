<?php

namespace App\Http\Resources;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'price'        => $this->price,
            'availability' => $this->availability,
            'hotel'        => new HotelResource(Hotel::findOrFail($this->hotel_id)), // HotelResource::collecte(Hotel::find($this->hotel_id))
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at
        ];
    }
}
