<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('tickets', function (Blueprint $table) {
            $table->integer('ticket_id')->primary()->autoIncrement();
            $table->string('title', 100);
            $table->text('description');
            $table->enum('status', ['nuevo', 'asignado', 'en_progreso', 'en_espera', 'resuelto', 'cerrado'])->index();
            $table->enum('worktype', ['electrico','telecom','planta_externa','civil']);
            $table->enum('alarmtype', ['hardware','software','red','seguridad']);
            $table->enum('priority', ['bajo','medio','alto','critico'])->index();
            //$table->string('inc_code', 10);
            //$table->string('category', 50);
            $table->integer('customer_id')->index();
            $table->foreign('customer_id')->references('customer_id')->on('customers');
            $table->integer('site_id')->index()->nullable();
            $table->foreign('site_id')->references('site_id')->on('sites');
            $table->string('created_by_uuid', 36)->index();
            $table->string('assigned_to_uuid', 36)->index()->nullable();
            $table->string('supervisor_uuid', 36)->index()->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
