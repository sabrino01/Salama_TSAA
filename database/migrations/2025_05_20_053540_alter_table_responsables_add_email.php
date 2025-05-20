<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableResponsablesAddEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('responsables', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('nom_utilisateur')->nullable();
            $table->string('mot_de_passe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('responsables',function (Blueprint $table){
            $table->dropColumn('email');
            $table->dropColumn('nom_utilisateur');
            $table->dropColumn('mot_de_passe');
        });
    }
}
