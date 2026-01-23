<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();

            // selekcija: prvi tim / youth / zene
            $table->enum('team_type', ['first_team', 'youth', 'women'])->index();

            // godište (npr 2006)
            $table->unsignedSmallInteger('birth_year');

            $table->unsignedSmallInteger('ime_i_prezime');

            // broj dresa
            $table->unsignedTinyInteger('shirt_number');

            // pozicija (držimo enum string, jednostavno)
            $table->enum('position', ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LM', 'RM', 'FC',])->index();

            // slika: "players/xxx.png"
            $table->string('photo')->nullable();

            // da možeš sakriti igrača bez brisanja
            $table->boolean('is_active')->default(true)->index();

            // unique: broj dresa ne može duplirati u istoj selekciji
            $table->unique(['team_type', 'shirt_number']);

            $table->timestamps();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('players');
    }
};
