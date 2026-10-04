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
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id');
            $table->string('titulo',255);
            $table->string('categoria',20)->default('desayuno')->index();
            $table->integer('tiempo')->default(1);
            $table->string('dificultad',20)->default('facil')->index();
            $table->text('ingredientes');
            $table->text('pasos');
            $table->text('nota')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
