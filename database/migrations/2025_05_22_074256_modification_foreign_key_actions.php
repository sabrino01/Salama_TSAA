<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificationForeignKeyActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            // Supprimer la contrainte étrangère
            $table->dropForeign(['responsables_id']);
            $table->dropForeign(['suivis_id']);

            // Supprimer les colonnes
            $table->dropColumn('responsables_id');
            $table->dropColumn('suivis_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
