<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');           // requested, approved, rejected, processed, failed, etc.
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('notes')->nullable();
            $table->string('actor_type')->default('customer'); // customer | admin | system | webhook
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Add UPI and photo fields to refunds
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('upi_id')->nullable()->after('bank_account_name');
            $table->string('proof_image_path')->nullable()->after('upi_id');
            $table->unsignedBigInteger('approved_by')->nullable()->after('proof_image_path');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->integer('refund_window_days')->default(30)->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_audit_logs');
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['upi_id', 'proof_image_path', 'approved_by', 'approved_at', 'refund_window_days']);
        });
    }
};
