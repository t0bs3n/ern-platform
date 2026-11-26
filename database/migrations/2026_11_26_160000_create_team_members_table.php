<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The board and the volunteers shown on About Us (R1-04). Editors change them.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('group', 20);
            $table->string('name', 120);
            $table->string('affiliation', 200)->nullable();
            $table->string('role_title', 120);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->index(['group', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
