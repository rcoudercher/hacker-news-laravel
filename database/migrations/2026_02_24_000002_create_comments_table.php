<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // HN item ID
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('story_id')->nullable();
            $table->string('by')->nullable();
            $table->text('text')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->boolean('dead')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->index('story_id');
            $table->index('parent_id');
            $table->index('by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
