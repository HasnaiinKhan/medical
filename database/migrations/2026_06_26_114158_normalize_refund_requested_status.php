<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize the incorrectly capitalised 'Refund_requested' status to 'refund_requested'.
     */
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'Refund_requested')
            ->update(['status' => 'refund_requested']);
    }

    public function down(): void
    {
        // Intentionally not reversing — the capital-R value was a bug.
    }
};
