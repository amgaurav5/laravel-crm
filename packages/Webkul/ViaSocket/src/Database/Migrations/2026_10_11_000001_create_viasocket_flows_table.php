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
            $table->unsignedInteger('user_id');
            $table->text('script_id');
            $table->string('title');
            $table->text('trigger');
            $table->longText('steps');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
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
