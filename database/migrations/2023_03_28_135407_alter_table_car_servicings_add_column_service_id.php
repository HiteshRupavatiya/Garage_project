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
        Schema::table('car_servicings', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->after('car_id');
            $table->foreign('service_id')->references('id')->on('service_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_servicings', function (Blueprint $table) {
            $table->dropForeign('car_servicings_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
};
