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
            // Drop existing constraints using explicit constraint names
            $table->dropForeign(['merged_into_id']);
            $table->dropForeign(['merge_record_id']);
            
            // Recreate with proper constraints
            $table->foreign('merged_into_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('merge_record_id')
                ->references('id')
                ->on('contact_merges')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::table('contact_merges', function (Blueprint $table) {
            // Drop existing constraints using explicit constraint names
            $table->dropForeign(['master_contact_id']);
            $table->dropForeign(['merged_contact_id']);
            
            // Recreate with proper constraints
            $table->foreign('master_contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('merged_contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop the new constraints
            $table->dropForeign(['merged_into_id']);
            $table->dropForeign(['merge_record_id']);
            
            // Recreate original constraints (without onDelete)
            $table->foreign('merged_into_id')
                ->references('id')
                ->on('contacts');

            $table->foreign('merge_record_id')
                ->references('id')
                ->on('contact_merges');
        });

        Schema::table('contact_merges', function (Blueprint $table) {
            // Drop the new constraints
            $table->dropForeign(['master_contact_id']);
            $table->dropForeign(['merged_contact_id']);
            
            // Recreate original constraints (without onDelete)
            $table->foreign('master_contact_id')
                ->references('id')
                ->on('contacts');

            $table->foreign('merged_contact_id')
                ->references('id')
                ->on('contacts');
        });
    }
};
