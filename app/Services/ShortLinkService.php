<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class ShortLinkService
{
    public const TOTAL_DAYS_FREE    = 3;
    public const TOTAL_DAYS_PREMIUM = 7;

    public function store(array $data): ShortLink
    {
        $days = self::TOTAL_DAYS_FREE;

        if (filled($data['user_id'] ?? null)) {
            $user = User::find($data['user_id']);

            if ($user->is_premium) {
                $data['is_premium'] = true;
                $days               = self::TOTAL_DAYS_PREMIUM;
            }
        }

        return ShortLink::create($data + [
            'code'       => $this->generateNewCode(),
            'expired_at' => now()->addDays($days),
        ]);
    }

    /**
     * @return Builder<ShortLink>
     */
    public function queryRedirect(): Builder
    {
        return ShortLink::query()
            ->select(['id', 'endpoint'])
            ->onlyValidated(true);
    }

    private function generateNewCode(): string
    {
        $total    = 4;
        $iterator = 0;

        do {
            $code      = mb_strtoupper(str()->random($total));
            $shortLink = ShortLink::query()->whereCode($code)->first();
            $exist     = (bool) $shortLink?->id;

            if ($shortLink && $shortLink->expired_at >= now()) {
                $exist = true;
            }

            if ($iterator % 10) {
                ++$total;
            }
            ++$iterator;
        } while (true === $exist);

        return $code;
    }
}
