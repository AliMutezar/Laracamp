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
        Schema::create('camp_benefits', function (Blueprint $table) {
            $table->id();

            // kalo mau pake foreignId ngga usah bikin field Fk-nya
            // $table->unsignedBigInteger('camp_id');
            // $table->foreign('camp_id')->references('id')->on('camps')->onDelete('cascade');

            $table->foreignId('camp_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_benefits');
    }
};
