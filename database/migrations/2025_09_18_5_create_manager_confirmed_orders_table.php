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
        Schema::create('manager_confirmed_orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('confirmation_status', ['approved', 'rejected'])->default('approved');
            $table->text('remarks')->nullable();
            $table->timestamp('confirmed_at')->useCurrent()->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('manager_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_confirmed_orders');
    }
};
