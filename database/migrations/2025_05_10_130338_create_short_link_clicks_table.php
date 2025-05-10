<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('short_link_clicks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('short_link_id')->constrained();
            $table->foreignId('geo_ip_id')->nullable()->constrained();
            $table->string('endpoint');
            $table->ipAddress()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_link_clicks');
    }
};
