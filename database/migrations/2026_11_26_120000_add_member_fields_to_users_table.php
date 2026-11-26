<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The member's details (R0-05). A user may sign in only once approved_at is set.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->after('id');
            $table->string('last_name', 100)->after('first_name');
            $table->string('slug')->unique()->after('last_name');
            $table->string('institution')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('career_stage', 50)->nullable();
            $table->text('interests')->nullable();
            $table->string('orcid', 19)->nullable();
            $table->string('discord_name', 100)->nullable();
            $table->boolean('listed_in_directory')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'first_name', 'last_name', 'slug', 'institution', 'country', 'career_stage',
                'interests', 'orcid', 'discord_name', 'listed_in_directory', 'approved_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->default('');
        });
    }
};
