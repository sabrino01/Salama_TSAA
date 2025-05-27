<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsSuivisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions_suivis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actions_id')->nullable()->constrained('actions');
            $table->foreignId('responsables_id')->nullable()->constrained('responsables');
            $table->string('statut_suivi')->nullable();
            $table->text('observation_suivi')->nullable();
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
        Schema::dropIfExists('actions_suivis');
    }
}
