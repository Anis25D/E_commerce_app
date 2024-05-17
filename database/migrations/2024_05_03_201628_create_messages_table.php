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

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            // $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->text('content');
            $table->string('status')->default('unseen');
            $table->unsignedBigInteger('sender_id'); 
            $table->unsignedBigInteger('receiver_id'); 
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};