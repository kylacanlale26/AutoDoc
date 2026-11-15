<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('upload_settings')->updateOrInsert(
            ['id' => 1],
            [
                'max_file_size_mb' => 10,
                'accepted_formats' => json_encode(['pdf', 'png', 'jpg', 'jpeg']),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('upload_settings')->where('id', 1)->update([
            'max_file_size_mb' => 10,
            'accepted_formats' => json_encode(['pdf']),
            'updated_at' => now(),
        ]);
    }
};
