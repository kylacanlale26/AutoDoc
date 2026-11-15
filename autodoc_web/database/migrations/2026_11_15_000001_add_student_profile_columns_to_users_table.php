<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {

        if (!Schema::hasTable('users') && Schema::hasTable('user')) {
            Schema::rename('user', 'users');
        }

        if (!Schema::hasTable('users')) {
            throw new RuntimeException('Expected an existing user table named user or users.');
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'student_number')) {
                $table->string('student_number', 50)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'course')) {
                $table->string('course', 120)->nullable();
            }
            if (!Schema::hasColumn('users', 'year_level')) {
                $table->string('year_level', 30)->nullable();
            }
            if (!Schema::hasColumn('users', 'section')) {
                $table->string('section', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'account_role')) {
                $table->string('account_role', 30)->default('student');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
