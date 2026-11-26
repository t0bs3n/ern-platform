<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The member's own choices and requests (R1-12). Every choice starts switched off.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('open_to_collaboration')->default(false);
            $table->boolean('show_discord_in_directory')->default(false);
            $table->string('pending_email')->nullable();
            $table->timestamp('pending_email_requested_at')->nullable();
            $table->timestamp('membership_end_requested_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['open_to_collaboration', 'show_discord_in_directory', 'pending_email', 'pending_email_requested_at', 'membership_end_requested_at']);
        });
    }
};
