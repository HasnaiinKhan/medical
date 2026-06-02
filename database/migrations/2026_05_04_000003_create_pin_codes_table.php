<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pin_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->string('area');
            $table->string('post_office')->nullable();
            $table->string('city')->default('Ahmedabad');
            $table->string('state')->default('Gujarat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pin_codes');
    }
};
