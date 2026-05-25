<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        DB::table('settings')->insert([
            ['key' => 'admin_email_notifications', 'value' => '1',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_whatsapp_notifications','value' => '0',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_email',                'value' => env('MAIL_FROM_ADDRESS', ''), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_whatsapp_number',      'value' => env('WHATSAPP_NUMBER', ''),   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_api_token',         'value' => '',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_phone_number_id',   'value' => '',     'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
