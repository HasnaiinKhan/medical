<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('delivery_pin', 6);
            $table->string('delivery_area');
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->unsignedBigInteger('subtotal_paise');
            $table->unsignedInteger('delivery_fee_paise')->default(0);
            $table->unsignedBigInteger('total_paise');
            $table->string('payment_method')->default('cod');
            $table->string('status')->default('placed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
