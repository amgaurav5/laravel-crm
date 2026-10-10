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
        Schema::create('viasocket_flow_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flow_id');
            $table->string('event')->nullable();
            $table->boolean('ok')->default(false);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('message', 500)->nullable();
            $table->timestamps();

            $table->foreign('flow_id')->references('id')->on('viasocket_flows')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viasocket_flow_runs');
    }
};
