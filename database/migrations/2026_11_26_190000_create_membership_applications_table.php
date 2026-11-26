<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Applications for membership (R1-09), kept apart from users. R1-10 decides them
     * and deletes the personal data of declined ones.
     */
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->index();
            $table->string('institution');
            $table->string('country', 100);
            $table->string('city', 100)->nullable();
            $table->string('career_stage', 50);
            $table->text('interests')->nullable();
            $table->string('discord_name', 100)->nullable();
            $table->timestamp('agreed_at');
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
