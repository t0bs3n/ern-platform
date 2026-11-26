<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The ERN conferences (R1-06). The full conference pages come in release 4.
     */
    public function up(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 40);
            $table->string('title')->nullable();
            $table->string('theme');
            $table->string('place');
            $table->string('mode', 40);
            $table->string('host')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 16)->default('upcoming');
            $table->text('intro')->nullable();
            $table->longText('summary')->nullable();
            $table->json('facts')->nullable();
            $table->json('key_dates')->nullable();
            $table->string('book_pdf_url')->nullable();
            $table->string('videos_url')->nullable();
            $table->string('old_summary_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
