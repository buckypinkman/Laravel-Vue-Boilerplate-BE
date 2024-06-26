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
        Schema::create('saving_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_account_id');
            $table->foreignId('saving_account_withdrawal_id');
            $table->integer('amount');
            $table->datetime('transaction_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned']);
            $table->foreignId('processed_by')->constrained('users');
            $table->datetime('processed_at');
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index()->nullable();
            $table->unsignedBigInteger('deleted_by')->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_account_transactions');
    }
};
