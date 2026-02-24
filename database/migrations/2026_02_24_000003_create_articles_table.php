<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('story_id')->unique();
            $table->string('url', 2048);
            $table->longText('content')->nullable();
            $table->string('content_type')->nullable();
            $table->string('fetch_status')->default('pending'); // pending, success, failed, timeout, skipped
            $table->text('fetch_error')->nullable();
            $table->unsignedInteger('fetch_duration_ms')->nullable();
            $table->timestamps();

            $table->index('fetch_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
