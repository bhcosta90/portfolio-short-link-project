<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('geo_ip_short_link_click', function (Blueprint $table) {
            $table->foreignId('geo_ip_id')->constrained();
            $table->foreignId('short_link_click_id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geo_ip_short_link_click');
    }
};
