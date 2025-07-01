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

        Schema::create('assignments', function (Blueprint $table) {
            $table->integer('assignment_id')->primary()->autoIncrement();
            $table->integer('ticket_id')->index();
            $table->foreign('ticket_id')->references('ticket_id')->on('tickets');
            $table->string('technician_uuid', 36)->index();
            $table->string('supervisor_uuid', 36)->index();
            $table->timestamp('assigned_at')->nullable()->useCurrent();
            $table->timestamp('due_date')->nullable();
            $table->enum('status', [""])->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
