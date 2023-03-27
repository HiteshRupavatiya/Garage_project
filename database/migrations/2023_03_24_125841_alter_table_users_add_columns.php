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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_verification_token');
            $table->bigInteger('phone')->unique()->nullable(false)->after('email');
            $table->string('profile_picture')->nullable(false);
            $table->enum('type', ['Customer', 'Mechanic', 'Garage Owner', 'Admin'])->default('Customer');
            $table->string('billable_name')->nullable(false);
            $table->string('address1')->nullable(false);
            $table->string('address2')->nullable();
            $table->bigInteger('zip_code')->nullable(false);
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('garage_id')->nullable()->constrained('garages');
            $table->foreignId('service_type_id')->nullable()->constrained('service_types');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('profile_picture');
            $table->dropColumn('type');
            $table->dropColumn('billable_name');
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('zip_code');
            $table->dropForeign('users_city_id_foreign');
            $table->dropColumn('city_id');
            $table->dropForeign('users_garage_id_foreign');
            $table->dropColumn('garage_id');
            $table->dropForeign('users_service_type_id_foreign');
            $table->dropColumn('service_type_id');
        });
    }
};
