<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
      public function up(): void
      {
            Schema::create('payments', function (Blueprint $table) {
                  $table->id();
                $table->foreignId('user_id');
                $table->foreignId('order_id');
                $table->unsignedInteger('total_in_cents');
                $table->string('status');
                $table->string('payment_gateway');
                $table->string('payment_id');
                  $table->timestamps();
            });
      }

      public function down(): void
      {
            Schema::dropIfExists('payments');
      }
};
