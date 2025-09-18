<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id');
            $table->string('product_name', 255);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->string('image_path', 255)->nullable();

            // Extra manager confirmation column
            $table->enum('MangerConfirm', ['pending', 'processing', 'completed', 'cancelled'])
                ->default('pending');

            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                ->default('pending');

            $table->string('confirmed_by', 255)->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
