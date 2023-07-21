<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\City;
use App\Models\Media;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'location'        => $this->location,
            'hotel_latitude'  => $this->hotel_latitude,
            'hotel_longitude' => $this->hotel_longitude,
            'description'     => $this->description,
            'thumbnail'       => new MediaResource(Media::find($this->thumbnail)),
            'images'          => MediaResource::collection(Media::ByIds($this->images)),
            'city'            => new CityResource(City::find($this->city_id)),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at
        ];
    }
}
