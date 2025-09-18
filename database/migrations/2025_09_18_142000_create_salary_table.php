<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary', function (Blueprint $table) {
            $table->id('salary_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('salary', 10, 2);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate();

            // (Optional) If you have a users table, you could add:
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary');
    }
};
