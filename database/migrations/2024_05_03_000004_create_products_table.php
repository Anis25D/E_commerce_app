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
        Schema::disableForeignKeyConstraints();
       
    
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade'); 
            $table->string('name');
            $table->string('brand')->nullable();
            $table->float('price');
            $table->float('onsale_price');
            $table->dateTime('created_at');
            $table->integer('purchase_nbr')->default(0);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); 
            $table->text('description');
            $table->float('rating')->nullable();
            $table->bigInteger('quantity'); 
        });
    
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
