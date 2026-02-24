<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // HN item ID
            $table->string('type')->default('story');
            $table->string('by')->nullable();
            $table->string('title')->nullable();
            $table->string('url', 2048)->nullable();
            $table->text('text')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('descendants')->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->boolean('dead')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->index('score');
            $table->index('posted_at');
            $table->index('by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
