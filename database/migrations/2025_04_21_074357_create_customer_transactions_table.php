<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable();
            $table->string('credit_amount')->nullable();
            $table->string('debit_amount')->nullable();
            $table->string('opening_balance')->nullable();
            $table->string('closing_balance')->nullable();
            $table->string('source')->nullable();
            $table->string('bank_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
