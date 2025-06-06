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
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->text('note')->nullable()->after('status'); // Tambahkan kolom note setelah status
        });
    }
    
    public function down()
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
    
};
