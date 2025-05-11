<?php

declare(strict_types = 1);

namespace App\Actions\ShortLink;

use App\Models\ShortLink;
use App\Models\User;
use Core\Validation\ValidateAll;

final readonly class CreateShortLinkAction
{
    use ValidateAll;

    public function handle(array $data): ShortLink
    {
        $dataValidate = $this->validate($data);

        $user = $this->getUser($dataValidate['user_id'] ?? null);
        $days = $this->getExpirationDays($user);

        $data = $this->prepareData($dataValidate, $user, $days);

        return ShortLink::create($data);
    }

    private function generateNewCode(): string
    {
        $total    = 6;
        $iterator = 0;

        do {
            $code  = $this->generateRandomCode($total);
            $exist = $this->codeExists($code);

            if (0 === $iterator % 10) {
                ++$total;
            }
            ++$iterator;
        } while ($exist);

        return $code;
    }

    private function getUser(?int $userId): ?User
    {
        return filled($userId)
            ? User::find($userId)
            : null;
    }

    private function getExpirationDays(?User $user): int
    {
        return $user?->is_premium
            ? ShortLink::TOTAL_DAYS_PREMIUM
            : ShortLink::TOTAL_DAYS_FREE;
    }

    private function prepareData(array $data, ?User $user, int $days): array
    {
        return $data + [
            'is_premium' => $user?->is_premium ?? false,
            'code'       => $this->generateNewCode(),
            'expired_at' => now()->addDays($days),
        ];
    }

    private function generateRandomCode(int $length): string
    {
        return mb_strtoupper(str()->random($length));
    }

    private function codeExists(string $code): bool
    {
        $shortLink = ShortLink::query()->whereCode($code)->first();

        return $shortLink?->id && $shortLink->expired_at >= now();
    }
}
