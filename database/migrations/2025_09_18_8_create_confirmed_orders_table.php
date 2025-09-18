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
        Schema::create('confirmed_orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');

            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                ->default('pending');
            $table->enum('payment_method', ['cod', 'card', 'bkash', 'nagad', 'rocket'])
                ->default('cod');

            $table->timestamp('confirmed_at')->useCurrent()->nullable();
            $table->text('delivery_address');
            $table->enum('delivery_status', ['pending', 'shipped', 'delivered', 'returned'])
                ->default('pending');
            $table->string('tracking_number', 100)->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');

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
        Schema::dropIfExists('confirmed_orders');
    }
};
