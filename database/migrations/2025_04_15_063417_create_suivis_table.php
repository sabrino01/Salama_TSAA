<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuivisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivis', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('description');
            $table->dateTimeTz('created_at', 0)->nullable();
            $table->dateTimeTz('updated_at', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suivis');
    }
}
