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
        Schema::create('garages', function (Blueprint $table) {
            $table->id();
            $table->string('garage_name')->unique()->nullable(false);
            $table->text('address1')->nullable(false);
            $table->text('address2')->nullable();
            $table->bigInteger('zip_code');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('state_id')->constrained('states');
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('garages');
    }
};
