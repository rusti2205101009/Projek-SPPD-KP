<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserProfileCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    public $collects = UserProfileResource::class;

    public function toArray(Request $request): array
    {
        return UserProfileResource::collection($this->collection)->toArray($request);
    }
}
