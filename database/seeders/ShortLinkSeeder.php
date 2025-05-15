<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\GeoIp;
use App\Models\ShortLink;
use App\Services\ShortLinkClickService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ShortLinkSeeder extends Seeder
{
    public function __construct(
        private readonly ShortLinkClickService $linkClickService,
    ) {
    }

    public function run(): void
    {
        DB::transaction(function (): void {
            $shortLink = ShortLink::factory()->create([
                'code' => 'INFXLU',
            ]);

            foreach ($this->getIps() as $ip => $value) {
                GeoIp::create([
                    'ip_address' => $ip,
                    'is_success' => true,
                ] + $value);

                $total = match ($ip) {
                    '201.86.224.1' => 3,
                    '189.56.120.1' => 2,
                    '189.28.64.1'  => 4,
                    '8.8.8.8'      => 2,
                    '1.1.1.1'      => 3,
                    '24.114.0.1'   => 2,
                    default        => 1,
                };

                for ($i = 0; $i < $total; ++$i) {
                    $this->linkClickService->store([
                        'id'         => $shortLink->id,
                        'endpoint'   => $shortLink->endpoint,
                        'ip_address' => $ip,
                    ]);
                }
            }

            ShortLink::factory()->create([
                'code'    => 'INFXLA',
                'user_id' => 1,
                'slug'    => 'testing-slug',
            ]);
        });
    }

    private function getIps(): array
    {
        return [
            '187.72.192.10'  => ['region' => 'SP', 'region_name' => 'São Paulo', 'country' => 'Brazil', 'country_code' => 'BR'],
            '201.86.224.1'   => ['region' => 'SP', 'region_name' => 'Campinas', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.56.120.1'   => ['region' => 'SP', 'region_name' => 'São José dos Campos', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.112.0.1'    => ['region' => 'RJ', 'region_name' => 'Rio de Janeiro', 'country' => 'Brazil', 'country_code' => 'BR'],
            '191.252.0.1'    => ['region' => 'SP', 'region_name' => 'São Paulo', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.71.128.1'   => ['region' => 'MG', 'region_name' => 'Belo Horizonte', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.13.144.1'   => ['region' => 'MG', 'region_name' => 'Uberlândia', 'country' => 'Brazil', 'country_code' => 'BR'],
            '187.5.128.1'    => ['region' => 'RS', 'region_name' => 'Porto Alegre', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.36.96.1'    => ['region' => 'RS', 'region_name' => 'Caxias do Sul', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.28.64.1'    => ['region' => 'BA', 'region_name' => 'Salvador', 'country' => 'Brazil', 'country_code' => 'BR'],
            '179.108.160.1'  => ['region' => 'BA', 'region_name' => 'Feira de Santana', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.12.240.1'   => ['region' => 'PE', 'region_name' => 'Recife', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.90.128.1'   => ['region' => 'PE', 'region_name' => 'Olinda', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.101.160.1'  => ['region' => 'CE', 'region_name' => 'Fortaleza', 'country' => 'Brazil', 'country_code' => 'BR'],
            '187.53.192.1'   => ['region' => 'AM', 'region_name' => 'Manaus', 'country' => 'Brazil', 'country_code' => 'BR'],
            '187.4.160.1'    => ['region' => 'DF', 'region_name' => 'Brasília', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.10.96.1'    => ['region' => 'PR', 'region_name' => 'Curitiba', 'country' => 'Brazil', 'country_code' => 'BR'],
            '179.124.112.1'  => ['region' => 'MA', 'region_name' => 'São Luís', 'country' => 'Brazil', 'country_code' => 'BR'],
            '189.39.208.1'   => ['region' => 'PI', 'region_name' => 'Teresina', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.66.160.1'   => ['region' => 'AP', 'region_name' => 'Macapá', 'country' => 'Brazil', 'country_code' => 'BR'],
            '138.219.112.1'  => ['region' => 'RS', 'region_name' => 'Porto Alegre', 'country' => 'Brazil', 'country_code' => 'BR'],
            '170.82.160.1'   => ['region' => 'SC', 'region_name' => 'Florianópolis', 'country' => 'Brazil', 'country_code' => 'BR'],
            '138.99.240.1'   => ['region' => 'SC', 'region_name' => 'Joinville', 'country' => 'Brazil', 'country_code' => 'BR'],
            '131.108.96.1'   => ['region' => 'PR', 'region_name' => 'Londrina', 'country' => 'Brazil', 'country_code' => 'BR'],
            '168.0.88.1'     => ['region' => 'SC', 'region_name' => 'Blumenau', 'country' => 'Brazil', 'country_code' => 'BR'],
            '177.220.192.1'  => ['region' => 'RO', 'region_name' => 'Porto Velho', 'country' => 'Brazil', 'country_code' => 'BR'],
            '8.8.8.8'        => ['region' => 'California', 'region_name' => 'Google DNS', 'country' => 'USA', 'country_code' => 'US'],
            '1.1.1.1'        => ['region' => 'USA', 'region_name' => 'Cloudflare', 'country' => 'USA', 'country_code' => 'US'],
            '13.107.21.200'  => ['region' => 'US East', 'region_name' => 'Microsoft', 'country' => 'USA', 'country_code' => 'US'],
            '24.114.0.1'     => ['region' => 'Canada', 'region_name' => 'Rogers Communications', 'country' => 'Canada', 'country_code' => 'CA'],
            '91.198.174.192' => ['region' => 'Frankfurt', 'region_name' => 'Wikimedia Foundation', 'country' => 'Germany', 'country_code' => 'DE'],
            '62.210.0.1'     => ['region' => 'Paris', 'region_name' => 'Online S.A.', 'country' => 'France', 'country_code' => 'FR'],
            '51.140.0.1'     => ['region' => 'UK South', 'region_name' => 'Azure', 'country' => 'United Kingdom', 'country_code' => 'GB'],
            '133.242.0.1'    => ['region' => 'Japan', 'region_name' => 'Sakura Internet', 'country' => 'Japan', 'country_code' => 'JP'],
            '139.130.4.5'    => ['region' => 'Sydney', 'region_name' => 'Telstra', 'country' => 'Australia', 'country_code' => 'AU'],
            '118.189.0.1'    => ['region' => 'Singapore', 'region_name' => 'StarHub', 'country' => 'Singapore', 'country_code' => 'SG'],
            '41.0.0.1'       => ['region' => 'South Africa', 'region_name' => 'MTN Group', 'country' => 'South Africa', 'country_code' => 'ZA'],
        ];
    }
}
