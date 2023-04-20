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
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('transaction_status', 100)->after('camp_id');
            $table->string('midtrans_url')->nullable()->after('transaction_status');
            $table->string('midtrans_order_id')->nullable()->after('midtrans_url');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['transaction_status', 'midtrans_url', 'midtrans_order_id', 'midtrans_payment_type']);
        });
    }
};
