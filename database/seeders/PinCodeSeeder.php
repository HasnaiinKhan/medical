<?php

namespace Database\Seeders;

use App\Models\PinCode;
use Illuminate\Database\Seeder;

class PinCodeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = require database_path('data/ahmedabad_pincodes.php');

        foreach ($rows as $row) {
            PinCode::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'area' => $row['area'],
                    'post_office' => $row['post_office'] ?? null,
                    'city' => $row['city'] ?? 'Ahmedabad',
                    'state' => $row['state'] ?? 'Gujarat',
                ]
            );
        }
    }
}
