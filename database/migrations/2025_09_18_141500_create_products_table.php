<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 100);
            $table->string('description', 255)->nullable();
            $table->string('category', 50);
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->integer('stock_quantity');
            $table->string('unit', 20)->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('image_path', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Add constraints
        DB::statement('ALTER TABLE products ADD CONSTRAINT price CHECK (price > 50)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT stock_quantity CHECK (stock_quantity > 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
