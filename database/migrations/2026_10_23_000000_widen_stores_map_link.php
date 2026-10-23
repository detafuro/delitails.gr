<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Google Maps place URLs (and encoded Greek queries) run well past 255 chars.
        Schema::table('stores', function (Blueprint $table) {
            $table->text('map_link')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('map_link')->nullable()->change();
        });
    }
};
