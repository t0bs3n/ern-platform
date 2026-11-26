<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A NOT NULL timestamp without a default becomes "ON UPDATE CURRENT_TIMESTAMP" on MySQL or
 * MariaDB servers that run with explicit_defaults_for_timestamp switched off. Every approval or
 * decline would then overwrite the moment the applicant agreed. As a nullable column it keeps
 * its value. The join form always sets it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_applications', function (Blueprint $table) {
            $table->timestamp('agreed_at')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        // Going back would bring the overwriting back, so the safe column stays.
    }
};
