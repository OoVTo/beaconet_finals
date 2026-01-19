<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('finder_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['owner_id', 'finder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
