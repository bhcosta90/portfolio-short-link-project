<?php

declare(strict_types = 1);

namespace App\Actions\ShortLink;

use App\Services\ShortLinkService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

final class ImportByExcelAction implements ShouldQueue, ToModel, WithChunkReading
{
    public function __construct(protected int $userId)
    {
    }

    public function model(array $row)
    {
        $service = app(ShortLinkService::class);

        try {
            $service->store($this->userId, [
                'endpoint' => $row[0],
                'slug'     => $row[1],
            ]);

        } catch (ValidationException $exception) {
            Log::info($exception->getMessage());
        }
    }

    public function chunkSize(): int
    {
        return 1;
    }
}
