<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsResponsablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions_responsables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actions_id')->nullable()->constrained('actions');
            $table->foreignId("responsables_id")->nullable()->constrained('responsables');
            $table->string('statut_resp')->nullable();
            $table->text('observation_resp')->nullable();
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
        Schema::dropIfExists('actions_responsables');
    }
}
