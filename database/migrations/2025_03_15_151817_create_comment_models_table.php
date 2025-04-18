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
        Schema::create('comment_models', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('comment_models')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('discussion_id')->constrained('discussions')->onDelete('cascade');
            $table->unsignedInteger('reply_number')->nullable(); // User's unique reply number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_models', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    
        Schema::dropIfExists('comment_models');
    }
};
