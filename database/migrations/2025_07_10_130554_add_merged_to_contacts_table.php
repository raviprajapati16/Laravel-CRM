<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('merged_into_id')
                ->after('is_active')
                ->nullable()
                ->constrained('contacts');

            $table->foreignId('merge_record_id')
                ->after('merged_into_id')
                ->nullable()
                ->constrained('contact_merges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
};
