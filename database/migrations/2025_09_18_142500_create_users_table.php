<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('contact_number', 11);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('role', 255)->default('Customer');
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Add constraints
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_email_format CHECK (email LIKE '%@%')");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_password_length CHECK (CHAR_LENGTH(password) > 6)");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_contact_number_length CHECK (CHAR_LENGTH(contact_number) = 11)");

        // Add trigger for setting email_verified_at
        DB::unprepared('
            CREATE TRIGGER set_email_verified_at
            BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                IF NEW.email_verified_at IS NULL THEN
                    SET NEW.email_verified_at = NOW();
                END IF;
            END
        ');
    }

    public function down(): void
    {
        // Drop trigger first to avoid errors
        DB::unprepared('DROP TRIGGER IF EXISTS set_email_verified_at');

        Schema::dropIfExists('users');
    }
};
