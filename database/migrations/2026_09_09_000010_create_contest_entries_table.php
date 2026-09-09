<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contest_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 64)->nullable();
            $table->json('extra')->nullable();
            $table->boolean('accepted_terms')->default(true);
            $table->boolean('marketing_consent')->default(false);
            // 1 = winner, 2+ = runner-up (in order)
            $table->unsignedTinyInteger('award_rank')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            // One entry per email per contest.
            $table->unique(['contest_id', 'email']);
            $table->index(['contest_id', 'award_rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_entries');
    }
};
