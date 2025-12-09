<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_admin');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->string('locked_reason')->nullable()->after('locked_at');
            $table->integer('failed_login_attempts')->default(0)->after('locked_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_at', 'locked_reason', 'failed_login_attempts']);
        });
    }
};