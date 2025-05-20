<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\GeoIp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class GeoIpFactory extends Factory
{
    protected $model = GeoIp::class;

    public function definition(): array
    {
        return [
            'ip_address'   => $this->faker->ipv4(),
            'is_success'   => $this->faker->boolean(),
            'qtd_retries'  => $this->faker->randomNumber(),
            'country'      => $this->faker->country(),
            'country_code' => $this->faker->word(),
            'region'       => $this->faker->word(),
            'region_name'  => $this->faker->name(),
            'city'         => $this->faker->city(),
            'zip'          => $this->faker->postcode(),
            'lat'          => $this->faker->latitude(),
            'lon'          => $this->faker->randomFloat(),
            'timezone'     => $this->faker->word(),
            'isp'          => $this->faker->word(),
            'org'          => $this->faker->word(),
            'as'           => $this->faker->word(),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
    }
}
