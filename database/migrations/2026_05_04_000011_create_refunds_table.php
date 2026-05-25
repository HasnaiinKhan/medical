<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('refund_number')->unique();
            $table->string('refund_id_gateway')->nullable();
            $table->unsignedBigInteger('amount_paise');
            $table->string('status')->default('requested');
            // requested | approved | processing | processed | failed | rejected
            $table->string('type')->default('gateway');
            // gateway | cod_bank_transfer | store_credit
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Add refund-related columns to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_dispatched')->default(false)->after('status');
            $table->string('cancellation_reason')->nullable()->after('is_dispatched');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_dispatched', 'cancellation_reason']);
        });
    }
};
