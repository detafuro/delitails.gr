<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contest_draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('entries_count')->default(0);
            // Snapshot of the picked entries: [{rank, entry_id, name, email}]
            $table->json('result')->nullable();
            $table->boolean('is_automatic')->default(false);
            $table->timestamps();

            $table->index(['contest_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_draws');
    }
};
