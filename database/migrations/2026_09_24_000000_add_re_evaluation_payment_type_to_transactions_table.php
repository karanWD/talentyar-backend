<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('payment', Transaction::PAYMENT)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('payment', [
                Transaction::PAYMENT_WALLET,
                Transaction::PAYMENT_SUBSCRIPTION,
                Transaction::PAYMENT_SUBSCRIPTION_EXTENDED,
            ])->change();
        });
    }
};
