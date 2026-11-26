<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Values the platform keeps about itself (R0-08), such as the last scheduled run.
     */
    public function up(): void
    {
        Schema::create('system_values', function (Blueprint $table) {
            $table->string('key', 64)->primary();
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_values');
    }
};
