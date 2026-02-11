<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();

            $table->string('team_type'); // first_team, youth, u19, u17, u15, women...
            $table->string('name');
            $table->string('role'); // trener, pomoćni, kondicioni, golmanski, fizioterapeut...
            $table->string('photo')->nullable(); // players/...webp

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['team_type', 'is_active']);
            $table->index(['team_type', 'sort_order']);
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('staff_members');
    }
};
