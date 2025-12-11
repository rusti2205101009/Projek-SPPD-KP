<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SptCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        // return [
        //     'data' => SptResource::collection($this->collection),
        // ];

        // return SptResource::collection($this->collection)->resolve();

        return SptResource::collection($this->collection)->toArray($request);
    }

    // public function with(Request $request): array
    // {
    //     return [
    //         'meta' => [
    //             'total' => $this->total(),
    //             'per_page' => $this->perPage(),
    //             'current_page' => $this->currentPage(),
    //             'last_page' => $this->lastPage(),
    //         ]
    //     ];
    // }
}
