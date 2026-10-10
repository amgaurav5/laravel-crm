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
        Schema::create('viasocket_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('service_id');
            $table->text('auth_id');
            $table->text('script_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'service_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viasocket_connections');
    }
};
