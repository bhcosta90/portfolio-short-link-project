<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ShortLink */
final class ShortLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->hash_id,
            'user_id'    => $this->user_id,
            'slug'       => $this->slug,
            'short_link' => url($this->slug ?: $this->hash_id),
            'endpoint'   => $this->endpoint,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
