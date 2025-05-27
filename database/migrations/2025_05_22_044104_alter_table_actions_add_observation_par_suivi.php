<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableActionsAddObservationParSuivi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table){
            $table->foreignId('actions_responsables_id')->nullable()->constrained('actions_responsables');
            $table->foreignId('actions_suivis_id')->nullable()->constrained('actions_suivis');
            $table->text('observation_par_suivi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table){
            $table->dropColumn('actions_responsables_id');
            $table->dropColumn('actions_suivis_id');
            $table->dropColumn('observation_par_suivi');
        });
    }
}
