<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->default('el');
            $table->char('key_hash', 40);
            $table->text('key');
            $table->text('value');
            $table->timestamps();
            $table->unique(['locale', 'key_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
