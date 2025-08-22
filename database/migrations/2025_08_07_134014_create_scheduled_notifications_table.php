<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'commencement', 'retard', 'suivi'
            $table->string('frequence_type'); // 'ponctuel', 'hebdomadaire', etc.
            $table->unsignedBigInteger('entity_id'); // ID de l'entité concernée
            $table->string('entity_type'); // Type de l'entité (classe)
            $table->datetime('trigger_date'); // Date de déclenchement
            $table->datetime('target_date'); // Date cible de l'événement
            $table->text('notification_data'); // Données pour la notification
            $table->boolean('sent')->default(false);
            $table->datetime('sent_at')->nullable();
            $table->dateTimeTz('created_at', 0)->nullable();
            $table->dateTimeTz('updated_at', 0)->nullable();

            $table->index(['trigger_date', 'sent']);
            $table->index(['entity_id', 'entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_notifications');
    }
}
