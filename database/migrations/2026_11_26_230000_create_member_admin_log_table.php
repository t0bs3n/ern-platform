<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who changed whose roles, and who exported the member list (R1-15).
     * Ids and the role only: no names, no e-mail addresses.
     */
    public function up(): void
    {
        Schema::create('member_admin_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 20);
            $table->string('role', 32)->nullable();
            $table->string('detail', 40)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_admin_log');
    }
};
