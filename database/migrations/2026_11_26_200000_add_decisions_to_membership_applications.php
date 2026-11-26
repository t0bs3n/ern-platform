<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Decisions on applications (R1-10). The log keeps who decided what and when,
     * without the applicant's personal data, so it survives the deletion of declined applications.
     */
    public function up(): void
    {
        Schema::table('membership_applications', function (Blueprint $table) {
            $table->timestamp('decided_at')->nullable()->index();
        });

        Schema::create('membership_decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->index();
            $table->string('decision', 20);
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('applied_at');
            $table->timestamp('agreed_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_decisions');

        Schema::table('membership_applications', function (Blueprint $table) {
            $table->dropIndex(['decided_at']);
            $table->dropColumn('decided_at');
        });
    }
};
