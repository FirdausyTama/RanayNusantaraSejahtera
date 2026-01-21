<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('berat_total', 10, 2)->nullable()->after('total_pembayaran');
            $table->decimal('harga_per_kg', 12, 2)->nullable()->after('berat_total');
            $table->decimal('estimasi_ongkir', 14, 2)->nullable()->after('harga_per_kg');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['berat_total', 'harga_per_kg', 'estimasi_ongkir']);
        });
    }
};
