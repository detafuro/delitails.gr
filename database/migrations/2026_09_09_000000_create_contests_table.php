<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('prize')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('terms')->nullable();
            $table->longText('winner_message')->nullable();
            $table->string('banner_image')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_published')->default(false);
            $table->boolean('auto_draw')->default(true);
            $table->unsignedTinyInteger('winners_count')->default(1);
            $table->unsignedTinyInteger('runners_up_count')->default(2);
            // off | optional | required
            $table->string('phone_field', 16)->default('optional');
            $table->boolean('newsletter_opt_in')->default(true);
            $table->json('extra_fields')->nullable();
            $table->timestamp('drawn_at')->nullable();
            $table->timestamp('entries_purged_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();

            $table->index(['is_published', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
