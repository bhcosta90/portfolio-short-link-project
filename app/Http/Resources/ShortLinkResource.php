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
            'id'           => $this->id_hashed,
            'endpoint'     => $this->endpoint,
            'url_redirect' => url($this->slug ?: $this->id_hashed),
            'slug'         => $this->slug,
            'user_id'      => $this->user_id_hashed,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
