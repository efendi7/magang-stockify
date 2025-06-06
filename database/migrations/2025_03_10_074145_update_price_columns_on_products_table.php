<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->change();
            $table->decimal('sale_price', 15, 2)->change();
        });
    }
    
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->change(); // Kembali ke ukuran sebelumnya
            $table->decimal('sale_price', 10, 2)->change(); // Kembali ke ukuran sebelumnya
        });
    }
};
