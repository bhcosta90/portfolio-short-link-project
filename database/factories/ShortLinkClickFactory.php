<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\GeoIp;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class ShortLinkClickFactory extends Factory
{
    protected $model = ShortLinkClick::class;

    public function definition(): array
    {
        return [
            'short_link_id' => ShortLink::factory(),
            'endpoint'      => $this->faker->word(),
            'ip_address'    => $this->faker->ipv4(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),

            'geo_ip_id' => GeoIp::factory(),
        ];
    }
}
