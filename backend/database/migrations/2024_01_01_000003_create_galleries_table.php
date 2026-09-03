<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            // No real image upload/storage — just an array of image URLs
            // (or placeholder URLs), kept simple as a JSON column.
            $table->json('image_urls')->default('[]');
            // Random, unguessable token used to build the public share link
            // (e.g. /g/{slug}) — no login needed to view a gallery by its slug.
            $table->string('slug', 32)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
