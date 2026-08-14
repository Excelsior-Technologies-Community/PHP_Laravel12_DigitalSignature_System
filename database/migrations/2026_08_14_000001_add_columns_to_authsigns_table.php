<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authsigns', function (Blueprint $table) {
            $table->string('remember_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('authsigns', function (Blueprint $table) {
            $table->dropColumn(['remember_token', 'email_verified_at', 'is_admin', 'last_login_at']);
        });
    }
};
