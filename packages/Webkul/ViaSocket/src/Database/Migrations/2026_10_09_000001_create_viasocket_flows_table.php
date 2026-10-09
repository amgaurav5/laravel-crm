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
        Schema::create('viasocket_flows', function (Blueprint $table) {
            $table->id();
            $table->string('flow_id')->unique();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('webhook_url');
            $table->string('status')->default('active');
            $table->text('payload')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viasocket_flows');
    }
};
