<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Events and deadlines that editors add (R1-08). Times are stored in UTC,
     * with the event's own time zone, in which they are shown.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('kind', 20);
            $table->boolean('all_day')->default(true);
            $table->date('date');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('timezone', 64)->default('Europe/Berlin');
            $table->string('place')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
