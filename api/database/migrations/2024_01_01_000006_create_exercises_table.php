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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->string('name');
            $table->string('muscle_group')->nullable();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('sets')->default(3);
            $table->string('reps');
            $table->string('rest')->default('60s');
            $table->string('load')->nullable();
            $table->string('tempo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys and indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'workout_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
