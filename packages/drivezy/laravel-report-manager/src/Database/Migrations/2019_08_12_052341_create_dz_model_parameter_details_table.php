<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzModelParameterDetailsTable extends Migration {
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_model_parameter_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('model_id')->nullable();
            $table->string('parameter')->nullable();
            $table->string('display_name')->nullable();
            $table->string('default_value')->nullable();
            $table->boolean('visibility')->default(1);
            $table->unsignedInteger('parameter_type_id');
            $table->unsignedInteger('reference_model_id');
            $table->text('additional_query');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('model_id')->references('id')->on('dz_model_details');
            $table->foreign('parameter_type_id')->references('id')->on('dz_column_definitions');
            $table->foreign('reference_model_id')->references('id')->on('dz_model_details');
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_model_parameter_details');
    }
}
