<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sources_id')->constrained('sources');
            $table->foreignId('type_actions_id')->constrained('type_actions');
            $table->foreignId('responsables_id')->constrained('responsables');
            $table->foreignId('suivis_id')->constrained('suivis');
            $table->foreignId('constats_id')->constrained('constats');
            $table->foreignId('users_id')->constrained('users');
            $table->string('num_actions')->unique();
            $table->date('date');
            $table->text('frequence');
            $table->string('observation');
            $table->string('statut');
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
        Schema::dropIfExists('actions');
    }
}
