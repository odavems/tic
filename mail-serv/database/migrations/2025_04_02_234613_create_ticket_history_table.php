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

        Schema::create('ticket_history', function (Blueprint $table) {
            $table->integer('history_id')->primary()->autoIncrement();
            $table->integer('ticket_id')->index();
            $table->foreign('ticket_id')->references('ticket_id')->on('tickets');
            $table->string('user_uuid', 36)->index();
            $table->string('action', 50);
            $table->string('old_value', 255)->nullable();
            $table->string('new_value', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_history');
    }
};
