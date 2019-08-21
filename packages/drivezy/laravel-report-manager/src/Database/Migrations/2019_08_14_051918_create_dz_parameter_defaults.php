<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzParameterDefaults extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_parameter_defaults', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Follow camel casing');
            $table->string('backend_parse_phrase');
            $table->string('frontend_parse_phrase');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_parameter_defaults');
    }
}
