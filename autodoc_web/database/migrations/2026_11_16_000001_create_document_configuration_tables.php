<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        Schema::create('upload_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('max_file_size_mb')->default(10);
            $table->json('accepted_formats');
            $table->timestamps();
        });

        $now = now();

        DB::table('document_types')->insert([
            'name' => 'Birth Certificate',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('upload_settings')->insert([
            'id' => 1,
            'max_file_size_mb' => 10,
            'accepted_formats' => json_encode(['pdf', 'png', 'jpg', 'jpeg']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_settings');
        Schema::dropIfExists('document_types');
    }
};
