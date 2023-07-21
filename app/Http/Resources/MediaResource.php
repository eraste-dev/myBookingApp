<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'file_path' => asset($this->file_path),
            'type'      => $this->type,
            'extension' => $this->extension,
            'deleted'   => $this->deleted,
            'user'      => new UserResource(User::find($this->user_id)),
        ];
    }
}
