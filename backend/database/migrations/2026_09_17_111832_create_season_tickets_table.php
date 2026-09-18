<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('season_tickets', function (Blueprint $table) {
            $table->id();

            // Broj koji korisnik fizički ima na sezonskoj karti.
            $table->string('ticket_number')->unique();

            // Npr. 2026/27
            $table->string('season', 20);

            // Podaci vlasnika nisu obavezni za validaciju popusta.
            $table->string('holder_name')->nullable();

            $table->boolean('is_active')->default(true);

            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->timestamps();

            $table->index(['season', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_tickets');
    }
};
