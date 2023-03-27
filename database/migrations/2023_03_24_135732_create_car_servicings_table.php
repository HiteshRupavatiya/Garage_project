<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_servicings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_id')->constrained('garages')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['Initiated', 'In-Progress', 'Delay', 'Complete', 'Delivered'])->default('Initiated');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_servicings');
    }
};
