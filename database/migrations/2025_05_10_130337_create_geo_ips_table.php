<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('geo_ips', function (Blueprint $table): void {
            $table->id();
            $table->ipAddress();
            $table->boolean('is_success')->boolean();
            $table->unsignedTinyInteger('qtd_retries')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('region', 10)->nullable();
            $table->string('region_name')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lon', 10, 6)->nullable();
            $table->string('timezone')->nullable();
            $table->string('isp')->nullable();
            $table->string('org')->nullable();
            $table->string('as')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geo_ips');
    }
};
