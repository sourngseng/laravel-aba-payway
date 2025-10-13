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
        $tableName = config('aba-payway.transaction_table', 'aba_payway_transactions');
        
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->index();
            $table->string('merchant_id');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_option')->default('abapay');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('items')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'unknown'])->default('pending');
            $table->json('payment_request')->nullable();
            $table->json('payment_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['merchant_id', 'created_at']);
            $table->index('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('aba-payway.transaction_table', 'aba_payway_transactions');
        Schema::dropIfExists($tableName);
    }
};