<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promo_code_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('promo_code_id');
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->timestamp('used_at');
            
            // Ensure one user can only use a promo once
            $table->unique(['user_id', 'promo_code_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promo_code_usage');
    }
};