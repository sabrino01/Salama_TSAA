<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificationColumnResponsablesIdToSuivisIdInActionsSuivis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions_suivis', function (Blueprint $table) {
            $table->dropForeign(['responsables_id']);
            $table->foreignId('suivis_id')->nullable()->constrained('suivis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions_suivis', function (Blueprint $table) {
            $table->dropForeign(['suivis_id']);
            $table->dropColumn('suivis_id');
        });
    }
}
