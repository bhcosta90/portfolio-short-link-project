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
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'slug'       => $this->slug,
            'code'       => $this->code,
            'short_link' => $this->short_link,
            'endpoint'   => $this->endpoint,
            'expired_at' => $this->expired_at,
            'clicks'     => $this->whenCounted('shortLinkClicks_count'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
