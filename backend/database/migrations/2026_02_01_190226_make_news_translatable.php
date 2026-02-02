<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() : void
    {
        // 1. Dodaj privremena JSON polja
        Schema::table('news', function (Blueprint $table) {
            $table->json('title_i18n')->after('id');
            $table->json('excerpt_i18n')->nullable()->after('slug');
            $table->json('content_i18n')->nullable()->after('excerpt');
        });

        // 2. Prebaci postojeće stringove u sr-Latn
        DB::table('news')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('news')->where('id', $row->id)->update(['title_i18n'   => json_encode(['sr-Latn' => $row->title,]),
                                                                  'excerpt_i18n' => $row->excerpt ? json_encode(['sr-Latn' => $row->excerpt]) : null,
                                                                  'content_i18n' => $row->content ? json_encode(['sr-Latn' => $row->content]) : null,]);
            }
        });

        // 3. Obriši stare kolone
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'content']);
        });

        // 4. Preimenuj JSON kolone na originalna imena
        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('title_i18n', 'title');
            $table->renameColumn('excerpt_i18n', 'excerpt');
            $table->renameColumn('content_i18n', 'content');
        });
    }

    public function down() : void
    {
        // rollback svjesno ne implementiramo
    }
};