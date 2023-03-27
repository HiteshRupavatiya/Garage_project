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
        Schema::create('car_servicing_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_servicing_id')->constrained('car_servicings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('mechanic_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['Pending', 'In-Progress', 'Complete'])->default('Pending');
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
        Schema::dropIfExists('car_servicing_jobs');
    }
};
