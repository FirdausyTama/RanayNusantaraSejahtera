<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            $table->foreignId('pembelian_id')->nullable()->constrained('pembelians')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            $table->dropForeign(['pembelian_id']);
            $table->dropColumn('pembelian_id');
        });
    }
};
