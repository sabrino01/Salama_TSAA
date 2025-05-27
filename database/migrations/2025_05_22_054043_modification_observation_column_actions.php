<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificationObservationColumnActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('actions', function (Blueprint $table) {
            $table->text('observation')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('actions', function (Blueprint $table) {
            $table->string('observation')->change(); // ou ajuste selon la taille d'origine
        });
    }
}
