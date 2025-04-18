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
        Schema::dropIfExists('block');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('block', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

    }
};
