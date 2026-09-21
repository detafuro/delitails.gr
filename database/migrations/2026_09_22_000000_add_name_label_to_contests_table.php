<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            // Optional per-contest label for the name field (e.g. "Company name").
            $table->string('name_label', 120)->nullable()->after('phone_field');
        });
    }

    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('name_label');
        });
    }
};
