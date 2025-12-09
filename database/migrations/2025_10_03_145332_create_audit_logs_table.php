<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // e.g., 'login', 'create_game', 'delete_user'
            $table->string('model_type')->nullable(); // e.g., 'Game', 'User'
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description'); // Human-readable description
            $table->json('old_values')->nullable(); // Before changes
            $table->json('new_values')->nullable(); // After changes
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};